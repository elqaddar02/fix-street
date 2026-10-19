<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsToStatusChange;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Services\PublicUploadService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use RespondsToStatusChange;

    public const STATUSES = ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'REJECTED'];

    public function __construct(private readonly PublicUploadService $uploads)
    {
    }

    public function index(Request $request)
    {
        $status = $request->input('status');
        $sortBy = $request->input('sort', 'latest'); // 'latest', 'oldest', 'mostLiked'
        $search = trim((string) $request->input('q'));
        $categoryId = $request->input('category');
        $cityId = $request->input('city');

        $reportsQuery = Report::with(['user', 'category', 'city', 'district'])
            ->withCount('likes')
            ->when(in_array($status, self::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($cityId, fn ($q) => $q->where('city_id', $cityId))
            ->when($search !== '', function ($q) use ($search) {
                // "#123" (or just "123") finds a report by its reference.
                $id = ltrim($search, '#');

                $q->where(function ($q) use ($search, $id) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));

                    if (ctype_digit($id)) {
                        $q->orWhere('id', (int) $id);
                    }
                });
            });

        match ($sortBy) {
            'mostLiked' => $reportsQuery->orderByDesc('likes_count')->latest(),
            'oldest' => $reportsQuery->oldest(),
            default => $reportsQuery->latest(),
        };

        $reports = $reportsQuery->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $cities = City::whereHas('reports')->orderBy('name')->get();

        return view('admin.reports.index', compact('reports', 'status', 'sortBy', 'search', 'categoryId', 'cityId', 'categories', 'cities'));
    }

    public function show(Report $report)
    {
        $report->load(['user', 'category', 'city', 'district', 'quartier', 'dossier', 'comments.user']);
        $report->loadCount('likes');

        $recentConfirmations = $report->likes()->with('user')->latest()->take(8)->get();

        // What admins did to this report, newest first.
        $history = AuditLog::with('admin')
            ->where('model', 'Report')
            ->where('model_id', $report->id)
            ->latest()
            ->take(20)
            ->get();

        return view('admin.reports.show', compact('report', 'recentConfirmations', 'history'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate(['status' => 'required|in:' . implode(',', self::STATUSES)]);

        $previous = $report->status;
        $report->update(['status' => $request->status]);

        if ($previous !== $report->status) {
            logAdminAction('Status', 'Report', $report->id, "{$previous} → {$report->status}");
        }

        return $this->statusResponse(
            $request,
            __('admin.flash_report_status'),
            ['status' => $report->status]
        );
    }

    public function bulkUpdateStatus(Request $request)
    {
        $data = $request->validate([
            'report_ids' => 'required|array|min:1',
            'report_ids.*' => 'integer|exists:reports,id',
            'status' => 'required|in:' . implode(',', self::STATUSES),
        ]);

        $reports = Report::whereIn('id', $data['report_ids'])->where('status', '!=', $data['status'])->get(['id', 'status']);

        foreach ($reports as $report) {
            logAdminAction('Status', 'Report', $report->id, "{$report->status} → {$data['status']} (bulk)");
        }

        $updatedCount = Report::whereIn('id', $reports->pluck('id'))->update(['status' => $data['status']]);

        return back()->with('success', __('admin.flash_bulk_status', ['count' => $updatedCount]));
    }

    public function destroy(Report $report)
    {
        // A report attached to a case is evidence; the public side protects it too.
        if ($report->dossier_id) {
            return back()->with('error', __('admin.flash_report_in_dossier'));
        }

        $title = $report->title;
        $this->uploads->delete($report->image);
        $report->delete();

        logAdminAction('Delete', 'Report', $report->id, $title);

        return redirect()->route('admin.reports.index')->with('success', __('admin.flash_report_deleted'));
    }
}
