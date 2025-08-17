<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-secondary leading-tight">
            {{ __('Profile Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- User Card -->
                <div class="w-full md:w-1/4">
                    <div class="bg-white p-6 rounded-xl shadow sticky top-24">
                        <!-- User Avatar -->
                        <div class="flex flex-col items-center">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-full bg-primary/10 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="absolute bottom-0 right-0 bg-primary text-white p-1 rounded-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-secondary">{{ auth()->user()->name }}</h3>
                            <p class="text-sm text-secondary/80">{{ auth()->user()->email }}</p>
                            <div class="mt-2 px-3 py-1 text-xs font-medium bg-primary/10 text-primary rounded-full">
                                Member since {{ auth()->user()->created_at->format('M Y') }}
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <div class="space-y-2">
                                <div class="flex items-center text-sm text-secondary/80">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ auth()->user()->email }}
                                </div>
                                @if(auth()->user()->phone)
                                <div class="flex items-center text-sm text-secondary/80">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ auth()->user()->phone }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content & Navigation Combined -->
                <div class="w-full md:w-3/4" x-data="{ activeTab: 'profile' }">
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <!-- Navigation Tabs -->
                        <div class="border-b border-gray-200">
                            <nav class="flex px-6 space-x-8">
                                <button @click="activeTab = 'profile'"
                                        class="flex items-center py-4 text-sm font-medium transition relative"
                                        :class="activeTab === 'profile' ? 'text-primary border-b-2 border-primary' : 'text-secondary hover:text-primary hover:border-b-2 hover:border-primary/30'">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Profile Information
                                </button>

                                <button @click="activeTab = 'password'"
                                        class="flex items-center py-4 text-sm font-medium transition relative"
                                        :class="activeTab === 'password' ? 'text-primary border-b-2 border-primary' : 'text-secondary hover:text-primary hover:border-b-2 hover:border-primary/30'">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Update Password
                                </button>

                                <button @click="activeTab = 'delete'"
                                        class="flex items-center py-4 text-sm font-medium transition relative ml-auto"
                                        :class="activeTab === 'delete' ? 'text-red-600 border-b-2 border-red-600' : 'text-red-600 hover:text-red-700 hover:border-b-2 hover:border-red-600/30'">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete Account
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Contents -->
                        <div class="p-6">
                            <div x-show="activeTab === 'profile'">
                                @include('user.profile.partials.update-profile-information-form')
                            </div>

                            <div x-show="activeTab === 'password'" style="display: none;">
                                @include('user.profile.partials.update-password-form')
                            </div>

                            <div x-show="activeTab === 'delete'" style="display: none;">
                                @include('user.profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Smooth Scroll -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</x-app-layout>
