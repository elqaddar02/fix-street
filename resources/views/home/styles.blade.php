<style>
    .hero-gradient { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); }
    .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .card-hover:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    .stat-gradient { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); }
    .gradient-text { background: linear-gradient(135deg, #dc2626, #991b1b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .pulse-dot { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    .report-card-image { aspect-ratio: 16/9; object-fit: cover; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); }
    
    /* Stats Animation */
    .stat-counter { animation: fadeInUp 0.6s ease-out forwards; }
    .stat-percentage { animation: fadeInUp 0.6s ease-out forwards; }
    .stat-progress-circle { animation: fillCircle 2s ease-out forwards; }
    
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
    .float-animation { animation: float 3s ease-in-out infinite; }
    .fade-in { animation: fadeIn 1s ease-in; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-blink { animation: blink 1s infinite; }
    @keyframes blink { 0%, 50% { opacity: 1; } 51%, 100% { opacity: 0; } }
    @keyframes fillCircle { from { stroke-dashoffset: 282.7; } to { stroke-dashoffset: 0; } }
</style>

