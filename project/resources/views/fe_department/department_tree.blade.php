<div class="toggle-node" data-id="{{ $department->id }}">
    <button class="btn btn-sm btn-outline-secondary toggle-btn">
        <i class="fas fa-chevron-down"></i>
    </button>
    <a href="{{ route('departments.show', $department->id) }}" class="department-name" style="margin-left: 10px;">
        {{ $department->name }} ({{ $department->children->count() }})
    </a>
</div>
<div class="sub-tree sub-departments-{{ $department->id }}" style="display: none; margin-left: 20px;">
    @foreach ($department->children as $child)
        @include('fe_department.department_tree', ['department' => $child])
    @endforeach
</div>
