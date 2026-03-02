<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                        colors: {
                            brand: {
                                50:  '#FEF3EE',
                                100: '#FDE3D4',
                                200: '#FBC4A8',
                                300: '#F89D71',
                                400: '#F47038',
                                500: '#E8613C',
                                600: '#D4461E',
                                700: '#B03419',
                                800: '#8C2C1B',
                                900: '#722719',
                            },
                            surface: '#F4F1EC',
                        },
                        borderRadius: {
                            'xl': '12px',
                            '2xl': '20px',
                            '3xl': '28px',
                            '4xl': '36px',
                        }
                    }
                }
            }
        </script>
        <style type="text/tailwindcss">
            @layer components {
                /* ── Glassmorphism ── */
                .glass-card { 
                    @apply bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm rounded-3xl overflow-hidden transition-all duration-300;
                }
                .glass-card-dark {
                    @apply bg-gray-900/40 backdrop-blur-xl border border-white/10 shadow-xl rounded-3xl overflow-hidden;
                }
                
                /* ── Buttons ── */
                .btn { @apply inline-flex items-center justify-center font-bold text-sm rounded-2xl px-6 py-3 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed gap-2 active:scale-95; }
                .btn-primary { @apply btn bg-brand-500 text-white hover:bg-brand-600 focus:ring-brand-400 shadow-lg shadow-brand-500/20 hover:shadow-brand-500/40; }
                .btn-secondary { @apply btn bg-white/50 backdrop-blur-md text-gray-700 border border-white hover:bg-white/80 focus:ring-gray-300 shadow-sm; }
                .btn-ghost { @apply btn bg-transparent text-gray-500 hover:bg-white/40 hover:text-gray-900; }
                
                /* ── Forms ── */
                .form-group { @apply flex flex-col gap-2; }
                .form-label { @apply block text-sm font-semibold text-gray-500 px-1; }
                .input { 
                    @apply block w-full rounded-2xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200/50 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-500 sm:text-sm transition-all bg-white/40 backdrop-blur-sm; 
                }
                .select { @apply input appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%239ca3af%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem] bg-[right_0.75rem_center] bg-no-repeat pr-10; }
                
                /* ── Typography ── */
                .heading-1 { @apply text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight; }
                .heading-2 { @apply text-xl font-bold text-gray-900; }
                
                /* ── Cards Aliases ── */
                .card { @apply glass-card; }
                .stat-card-modern { @apply glass-card p-6; }
                .action-card { @apply glass-card p-5 flex items-center gap-4 hover:border-brand-300 hover:translate-y-[-2px] hover:shadow-md; }
                .table-card { @apply glass-card; }
                
                /* ── Sidebar ── */
                .sidebar-tooltip { 
                    @apply invisible opacity-0 translate-x-[-10px] group-hover:visible group-hover:opacity-100 group-hover:translate-x-0 absolute left-full ml-4 px-3 py-2 bg-gray-900/90 backdrop-blur-md text-white text-xs font-bold rounded-xl whitespace-nowrap z-[60] transition-all duration-300 pointer-events-none shadow-xl border border-white/10; 
                }
                
                /* ── Tables ── */
                .table-modern { @apply w-full border-separate border-spacing-0; }
                .table-modern th { @apply px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100/50; }
                .table-modern td { @apply px-6 py-5 text-sm text-gray-600 border-b border-gray-50/50 transition-all duration-300; }
                .table-modern tr:last-child td { @apply border-b-0; }
                
                /* ── Status Badges ── */
                .status-badge { @apply inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all; }
                .status-active { @apply bg-emerald-50 text-emerald-600; }
                .status-inactive { @apply bg-rose-50 text-rose-600; }
                .status-neutral { @apply bg-gray-100 text-gray-600; }

                /* ── Alerts ── */
                .alert-success { @apply p-4 rounded-2xl bg-emerald-500 text-white font-bold text-sm; }
                .alert-error { @apply p-4 rounded-2xl bg-rose-500 text-white font-bold text-sm; }

                /* ── Navigation Links ── */
                .link-back { @apply inline-flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-brand-500 uppercase tracking-widest mb-8 transition-colors; }
                .link { @apply text-brand-500 hover:text-brand-700 font-bold transition-colors; }

                /* ── Form Pages ── */
                .form-card { @apply glass-card p-8 sm:p-10; }
                .form-actions { @apply flex items-center gap-3 pt-4 border-t border-gray-100/30 mt-2; }
                .form-error { @apply text-xs font-bold text-rose-500 mt-1 px-1; }
                .form-section { @apply pt-6 mt-6 border-t border-gray-100/30; }
                .input-error { @apply ring-rose-400 focus:ring-rose-500; }
                .textarea { @apply input min-h-[80px] resize-y; }
                .checkbox { @apply w-5 h-5 rounded-lg border-gray-300 text-brand-500 focus:ring-brand-500 transition-all cursor-pointer; }

                /* ── Typography Extras ── */
                .heading-3 { @apply text-base font-black text-gray-900 uppercase tracking-widest; }
                .body { @apply text-sm font-medium text-gray-700; }
                .body-sm { @apply text-sm text-gray-500; }

                /* ── Detail / Show Pages ── */
                .detail-grid { @apply grid grid-cols-1 sm:grid-cols-2 gap-6 p-8; }
                .detail-item { @apply flex flex-col gap-1; }
                .detail-label { @apply text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]; }
                .detail-value { @apply text-sm font-bold text-gray-900; }
                .detail-total { @apply px-8 py-5 bg-gray-50/50 border-t border-gray-100/30 text-right font-black text-gray-900 text-lg tracking-tight; }

                /* ── Page Header ── */
                .page-header { @apply flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2; }

                /* ── Reveal Animation State ── */
                .reveal { 
                    @apply opacity-0 translate-y-8 blur-[2px] transition-all duration-[800ms] ease-[cubic-bezier(0.16,1,0.3,1)]; 
                }
                .reveal.active { 
                    @apply opacity-100 translate-y-0 blur-0; 
                }
            }
        </style>
    @endif
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.2); }
    </style>
</head>
<body class="h-full font-sans antialiased text-gray-900 overflow-x-hidden" style="background-color: #F4F1EC;">
    @auth
    <div class="flex h-full p-8 lg:p-10 gap-8">
        {{-- ═══ FLOATING ICON SIDEBAR ═══ --}}
        <aside class="hidden lg:flex lg:flex-col lg:z-50 lg:w-[72px] shrink-0">
            <div class="flex flex-col h-full bg-white/70 backdrop-blur-2xl border border-white/40 shadow-xl rounded-4xl items-center py-5 gap-y-3 select-none overflow-hidden">
                
                {{-- Logo Section --}}
                <div class="flex flex-col items-center">
                    <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-2xl flex items-center justify-center bg-brand-500 text-white font-extrabold text-[10px] tracking-tighter shadow-lg shadow-brand-500/30 hover:scale-110 active:scale-95 transition-all duration-300">
                        ER
                    </a>
                </div>

                <div class="w-8 h-[1px] bg-gray-200/50"></div>

                {{-- User Avatar --}}
                <div class="group relative flex items-center justify-center cursor-pointer">
                    <div class="w-9 h-9 rounded-2xl bg-brand-100 border-2 border-white text-brand-600 flex items-center justify-center text-xs font-bold shadow-md">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="sidebar-tooltip">{{ auth()->user()->name }}</span>
                </div>

                <div class="w-8 h-[1px] bg-gray-200/50"></div>

                {{-- Core Nav --}}
                <nav class="flex-1 flex flex-col items-center gap-y-0.5 w-full overflow-hidden px-2">
                    @php 
                        $navs = [
                            ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25', 'label' => 'Dashboard'],
                            ['route' => 'web.products.index', 'pattern' => 'web.products.*', 'icon' => 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9', 'label' => 'Products'],
                            ['route' => 'web.categories.index', 'pattern' => 'web.categories.*', 'icon' => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z', 'label' => 'Categories'],
                            ['route' => 'web.warehouses.index', 'pattern' => 'web.warehouses.*', 'icon' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0h.008v.008h-.008v-.008z', 'label' => 'Warehouses'],
                            ['route' => 'web.stock.index', 'pattern' => 'web.stock.*', 'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z', 'label' => 'Stock'],
                            ['route' => 'web.sales.index', 'pattern' => 'web.sales.*', 'icon' => 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z', 'label' => 'Sales'],
                            ['route' => 'web.expenses.index', 'pattern' => 'web.expenses.*', 'icon' => 'M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3', 'label' => 'Expenses', 'gate' => 'view-expenses'],
                            ['route' => 'web.refunds.index', 'pattern' => 'web.refunds.*', 'icon' => 'M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3', 'label' => 'Refunds'],
                            ['route' => 'web.accounts.index', 'pattern' => 'web.accounts.*', 'icon' => 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z', 'label' => 'Accounts', 'gate' => 'view-accounting'],
                            ['route' => 'web.journal-entries.index', 'pattern' => 'web.journal-entries.*', 'icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25', 'label' => 'Journal', 'gate' => 'view-accounting'],
                            ['route' => 'web.users.index', 'pattern' => 'web.users.*', 'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z', 'label' => 'Users', 'gate' => 'manage-users'],
                            ['route' => 'web.audit-logs.index', 'pattern' => 'web.audit-logs.*', 'icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.333 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.956 11.956 0 0112 2.714z', 'label' => 'Security Audit', 'is_audit' => true, 'gate' => 'view-audit-logs'],
                        ];
                    @endphp

                    @foreach($navs as $nav)
                        @if(!isset($nav['gate']) || auth()->user()->can($nav['gate']))
                            <a href="{{ route($nav['route']) }}" class="group relative flex items-center justify-center w-10 h-10 rounded-xl transition-all duration-300 {{ request()->routeIs($nav['pattern']) ? (isset($nav['is_audit']) ? 'bg-gray-900 text-white shadow-lg' : 'bg-brand-500 text-white shadow-lg shadow-brand-500/20') : 'text-gray-400 hover:bg-white hover:text-brand-500 hover:shadow-md' }}">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $nav['icon'] }}" /></svg>
                                <span class="sidebar-tooltip">{{ $nav['label'] }}</span>
                            </a>
                        @endif
                    @endforeach


                </nav>

                {{-- Bottom: Logout --}}
                <div class="flex flex-col items-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="group relative flex items-center justify-center w-10 h-10 rounded-xl text-gray-400 hover:bg-rose-50 hover:text-rose-500 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                            <span class="sidebar-tooltip">Log out</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ═══ MAIN WINDOW ═══ --}}
        <main class="flex-1 flex flex-col h-full bg-transparent">
            {{-- Content Window --}}
            <div class="flex-1 h-full rounded-4xl glass-card border border-white/50 relative overflow-y-auto no-scrollbar">
                
                {{-- Dynamic Grainy Texture --}}
                <div class="absolute inset-0 pointer-events-none opacity-[0.03] z-10" style="background-image: url('https://grainy-gradients.vercel.app/noise.svg');"></div>

                <div class="relative z-20 px-8 py-10 sm:px-12 lg:px-16 min-h-full flex flex-col">
                    @if (session('success'))
                        <div class="alert-success mb-8 reveal shadow-xl">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert-error mb-8 reveal shadow-xl">{{ session('error') }}</div>
                    @endif
                    
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    @else
    {{-- ═══ AUTHENTICATION VIEW ═══ --}}
    <div class="min-h-full flex flex-col items-center justify-center p-8 sm:p-12">
        <div class="w-full max-w-md">
            <div class="text-center mb-12 reveal">
                <div class="w-20 h-20 rounded-[40px] bg-brand-500 text-white flex items-center justify-center mx-auto mb-6 text-2xl font-black shadow-2xl shadow-brand-500/40 hover:scale-110 transition-transform duration-500 cursor-default">ER</div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">Access Control</h1>
                <p class="text-gray-400 mt-3 font-bold text-xs uppercase tracking-[0.2em]">Secure Gateway Portal</p>
            </div>
            
            <div class="glass-card p-10 sm:p-12 shadow-2xl reveal relative overflow-hidden">
                {{-- Decorative element --}}
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-brand-500/5 blur-[80px] rounded-full"></div>
                
                @yield('content')
            </div>

            <div class="mt-12 text-center reveal">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Precision ERP Infrastructure v4.0</p>
            </div>
        </div>
    </div>
    @endauth

    {{-- ═══ GLOBAL REVEAL SCRIPT ═══ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Apply reveal classes automatically
            const tags = ['section', '.card', '.stat-card-modern', 'table tr', '.page-header', '.form-group'];
            tags.forEach(tag => {
                document.querySelectorAll(tag).forEach(el => {
                    if(!el.classList.contains('reveal')) el.classList.add('reveal');
                });
            });

            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        // Staggered effect
                        setTimeout(() => {
                            entry.target.classList.add('active');
                        }, index * 50);
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
