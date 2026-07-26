@extends('layouts.app')
@section('page-title', __('messages.create_new_role'))
@section('content')
<div class="max-w-3xl">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
        <form method="POST" action="/admin/roles" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('messages.role_name_label') }}</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm" placeholder="{{ __('messages.role_name_placeholder') }}">
            </div>

            <div>
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-sm font-medium">{{ __('messages.set_permissions_label') }}</label>
                    <label class="flex items-center gap-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 cursor-pointer hover:text-zinc-900 dark:hover:text-white transition">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-zinc-900 bg-zinc-100 border-zinc-300 rounded focus:ring-zinc-900 dark:focus:ring-white dark:ring-offset-zinc-800 focus:ring-2 dark:bg-zinc-700 dark:border-zinc-600">
                        {{ __('messages.select_all') }}
                    </label>
                </div>
                
                @php 
                    $groupedPermissions = [
                        __('messages.dashboard_group') => ['dashboard.view'],
                        __('messages.system_management_group') => ['admin.access'],
                        __('messages.user_group') => ['user.view', 'user.manage', 'role.view', 'role.manage'],
                        __('messages.setting_group') => ['settings.manage'],
                    ];
                @endphp

                <div class="space-y-6">
                    @foreach($groupedPermissions as $groupName => $perms)
                        @php 
                            $validPerms = collect($perms)->filter(function($p) use ($permissions) {
                                return $permissions->contains('name', $p);
                            });
                        @endphp
                        @if($validPerms->count() > 0)
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 px-4 py-3 border-b border-zinc-200 dark:border-zinc-800">
                                <h3 class="text-sm font-semibold">{{ $groupName }}</h3>
                            </div>
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($validPerms as $perm)
                                <label class="flex items-center gap-3 p-3 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm }}" 
                                        class="perm-checkbox w-4 h-4 text-zinc-900 bg-zinc-100 border-zinc-300 rounded focus:ring-zinc-900 dark:focus:ring-white dark:ring-offset-zinc-800 focus:ring-2 dark:bg-zinc-700 dark:border-zinc-600">
                                    <span class="text-sm font-medium">{{ str_replace(['_', '.'], ' ', ucfirst($perm)) }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <button type="submit" class="px-6 py-2.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl text-sm font-semibold hover:bg-zinc-800 transition">{{ __('messages.create_role') }}</button>
                <a href="/admin/roles" class="px-6 py-2.5 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm font-medium hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function(e) {
    const checkboxes = document.querySelectorAll('.perm-checkbox');
    checkboxes.forEach(cb => cb.checked = e.target.checked);
});
</script>
@endsection
