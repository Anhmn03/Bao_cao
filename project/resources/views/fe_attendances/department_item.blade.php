<li>
    <span class="department" onclick="toggleSubDepartments(this)">{{ $department->name }}</span>
    @if($department->children->isNotEmpty())
        <ul style="display: none;">
            @foreach($department->children as $subDepartment)
                @include('fe_attendances.department_item', ['department' => $subDepartment])
            @endforeach
        </ul>
    @endif
</li>