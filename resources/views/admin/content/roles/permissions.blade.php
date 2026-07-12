@extends('admin.layouts.master')
@section('page-title', 'Role Permissions')

@push('admin-styles')
    <style>
        .permission-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .permission-chip {
            border: 1px solid #d9e2f0;
            border-radius: 6px;
            padding: .35rem .6rem;
            background: #fff;
            margin: 0;
            white-space: nowrap;
        }

        .permissions-table th,
        .permissions-table td {
            vertical-align: middle;
        }

        .permissions-table th:not(:first-child),
        .permissions-table td:not(:first-child) {
            text-align: center;
        }
    </style>
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb
            title="Role Permissions"
            :items="[
                ['label' => 'Roles & Permissions', 'url' => route('admin.users.roles.index')],
                ['label' => Str::headline($role->name)]
            ]"
        />

        <form method="POST" action="{{ route('admin.users.roles.update', $role) }}" id="role-permissions-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="update_permissions" value="1">

            <x-admin.card title="Permissions for {{ Str::headline($role->name) }}">
                <x-slot name="headerActions">
                    <a href="{{ route('admin.users.roles.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i> Save Permissions
                    </button>
                </x-slot>

                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <ul class="nav nav-tabs mb-3" id="permission-tabs" role="tablist">
                    @foreach($permissionGroups as $areaGroup)
                        @php
                            $areaKey = $areaGroup['area'] === 'pos' ? 'pos' : 'web';
                            $areaLabel = $areaGroup['area'] === 'pos' ? 'POS' : 'Web';
                        @endphp
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link {{ $loop->first ? 'active' : '' }}"
                                id="{{ $areaKey }}-permissions-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#{{ $areaKey }}-permissions-pane"
                                type="button"
                                role="tab"
                                aria-controls="{{ $areaKey }}-permissions-pane"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $areaLabel }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content" id="permission-tabs-content">
                    @foreach($permissionGroups as $areaGroup)
                        @php
                            $areaKey = $areaGroup['area'] === 'pos' ? 'pos' : 'web';
                            $areaLabel = $areaGroup['area'] === 'pos' ? 'POS' : 'Web';
                        @endphp
                        <div
                            class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            id="{{ $areaKey }}-permissions-pane"
                            role="tabpanel"
                            aria-labelledby="{{ $areaKey }}-permissions-tab"
                            tabindex="0">
                            <div class="border rounded-3 p-3 bg-light mb-3 d-flex justify-content-between align-items-center gap-3">
                                <div class="fw-semibold">{{ $areaLabel }} Permissions</div>
                                <div class="form-check mb-0">
                                    <input type="checkbox" class="form-check-input area-permission-toggle" id="select_all_{{ $areaKey }}_permissions" data-area="{{ $areaKey }}">
                                    <label class="form-check-label fw-semibold" for="select_all_{{ $areaKey }}_permissions">Select all in this tab</label>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle permissions-table mb-0">
                                    <thead class="table-light">
                                        <tr class="text-muted small text-uppercase">
                                            <th style="width: 22%;">Module</th>
                                            <th style="width: 8%;">View</th>
                                            <th style="width: 8%;">Create</th>
                                            <th style="width: 8%;">Edit</th>
                                            <th style="width: 8%;">Delete</th>
                                            <th>Other Permissions</th>
                                            <th style="width: 12%;">Module Select</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($areaGroup['modules'] as $group)
                                            @php($moduleKey = $areaKey.'-'.$group['slug'])
                                            <tr>
                                                <td class="fw-semibold">{{ $group['module'] }}</td>
                                                @foreach(['view', 'create', 'edit', 'delete'] as $action)
                                                    <td>
                                                        @if($group['standard'][$action])
                                                            <input
                                                                class="form-check-input permission-checkbox"
                                                                type="checkbox"
                                                                value="{{ $group['standard'][$action]['name'] }}"
                                                                name="permissions[]"
                                                                data-area="{{ $areaKey }}"
                                                                data-module="{{ $moduleKey }}"
                                                                @checked(in_array($group['standard'][$action]['name'], $assignedPermissions, true))>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-start">
                                                    @if(!empty($group['other']))
                                                        <div class="permission-chip-list">
                                                            @foreach($group['other'] as $permission)
                                                                <label class="permission-chip">
                                                                    <input
                                                                        class="form-check-input me-2 permission-checkbox"
                                                                        type="checkbox"
                                                                        value="{{ $permission['name'] }}"
                                                                        name="permissions[]"
                                                                        data-area="{{ $areaKey }}"
                                                                        data-module="{{ $moduleKey }}"
                                                                        @checked(in_array($permission['name'], $assignedPermissions, true))>
                                                                    <span>{{ $permission['label'] }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input module-permission-toggle" type="checkbox" id="toggle_module_{{ $moduleKey }}" data-area="{{ $areaKey }}" data-module="{{ $moduleKey }}">
                                                        <label class="form-check-label small" for="toggle_module_{{ $moduleKey }}">Select</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>
        </form>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
    $(function() {
        function syncModuleToggle(module) {
            const boxes = $('.permission-checkbox[data-module="' + module + '"]');
            $('#toggle_module_' + module).prop('checked', boxes.length && boxes.filter(':checked').length === boxes.length);
        }

        function syncAreaToggle(area) {
            const boxes = $('.permission-checkbox[data-area="' + area + '"]');
            $('.area-permission-toggle[data-area="' + area + '"]').prop('checked', boxes.length && boxes.filter(':checked').length === boxes.length);
        }

        function syncPaneModules(pane) {
            pane.find('.module-permission-toggle').each(function () {
                syncModuleToggle($(this).data('module'));
            });
        }

        $('.module-permission-toggle').each(function () {
            syncModuleToggle($(this).data('module'));
        });

        $('.area-permission-toggle').each(function () {
            syncAreaToggle($(this).data('area'));
        });

        $(document).on('change', '.module-permission-toggle', function () {
            const module = $(this).data('module');
            $('.permission-checkbox[data-module="' + module + '"]').prop('checked', $(this).is(':checked'));
            syncAreaToggle($(this).data('area'));
        });

        $(document).on('change', '.permission-checkbox', function () {
            syncModuleToggle($(this).data('module'));
            syncAreaToggle($(this).data('area'));
        });

        $(document).on('change', '.area-permission-toggle', function () {
            const pane = $(this).closest('.tab-pane');
            const checked = $(this).is(':checked');
            pane.find('.permission-checkbox, .module-permission-toggle').prop('checked', checked);
            syncPaneModules(pane);
        });
    });
</script>
@endpush
