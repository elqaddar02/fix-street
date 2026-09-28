<div class="grid md:grid-cols-2 gap-8">
    @forelse ($reports as $report)
        <article class="group relative flex flex-col rounded-[2.5rem] border border-slate-200 bg-white p-3 transition-all duration-500 hover:shadow-2xl hover:shadow-slate-200 hover:-translate-y-2">
            <div class="relative h-60 w-full rounded-[2rem] overflow-hidden bg-slate-100 shadow-inner">
                @if($report->image)
                    <img src="{{ asset('storage/' . $report->image) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                @else
                    <div class="flex items-center justify-center h-full bg-slate-50 border-2 border-dashed border-slate-100 rounded-[2rem] m-2">
                        <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                @endif

                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="px-4 py-1.5 bg-slate-900/80 backdrop-blur-md rounded-full text-[9px] font-black uppercase tracking-widest text-white shadow-lg">
                        {{ $report->status }}
                    </span>
                </div>

                <div class="absolute bottom-4 right-4 translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                    <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-2xl shadow-xl">
                        {{-- Confirmations, not likes: how many neighbours corroborate the problem. --}}
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path></svg>
                        <span class="text-xs font-black text-slate-900" title="{{ trans_choice('report.confirmations_count', $report->likes_count ?? 0, ['count' => $report->likes_count ?? 0]) }}">{{ $report->likes_count ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="p-6 flex-grow flex flex-col">
                <div class="flex items-center gap-3 mb-4 flex-wrap">
                    <span class="px-2.5 py-1 bg-red-50 rounded-lg text-[9px] font-black text-red-600 uppercase tracking-tighter">{{ $report->category->display_name }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $report->city->display_name }}</span>
                    @if($report->district)
                        <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ app()->getLocale() === 'ar' ? $report->district->name_ar : $report->district->name_fr }}</span>
                    @endif
                </div>

                {{-- The form only collects one piece of text, which is mirrored into
                     description, so printing both showed the same sentence twice. --}}
                <h4 class="text-xl font-black text-slate-900 mb-3 group-hover:text-red-600 transition-colors leading-tight line-clamp-3">{{ $report->title }}</h4>
                @if(filled($report->description) && $report->description !== $report->title)
                    <p class="text-sm text-slate-500 leading-relaxed line-clamp-2 mb-6 font-medium">{{ $report->description }}</p>
                @endif

                <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-900 flex items-center justify-center text-xs font-black text-white transform -rotate-3 group-hover:rotate-0 transition-transform">
                            {{ substr($report->user->name, 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black uppercase text-slate-900">{{ $report->user->name }}</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <a href="{{ route('reports.show', $report) }}" class="p-3 rounded-2xl bg-slate-50 text-slate-400 hover:bg-red-600 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </article>
    @empty
        <div class="col-span-full py-32 rounded-[3rem] border-2 border-dashed border-slate-200 bg-white/50 backdrop-blur-sm flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-lg font-black uppercase tracking-widest text-slate-900">{{ __('Archive Empty') }}</h3>
            <p class="text-sm text-slate-500 mt-2">{{ __('Adjust your filter parameters to see more entries.') }}</p>
        </div>
    @endforelse
</div>

<div class="pt-10">
    {{ $reports->links() }}
</div>
