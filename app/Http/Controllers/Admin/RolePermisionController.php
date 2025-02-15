<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermisionController extends Controller
{
    // Constructor với middleware để kiểm tra quyền cho từng hành động
    public function __construct()
    {
        $this->middleware(['permission:access management index,admin'])->only(['index']);
        $this->middleware(['permission:access management create,admin'])->only(['create', 'store']);
        $this->middleware(['permission:access management update,admin'])->only(['edit', 'update', 'handleTitle']);
        $this->middleware(['permission:access management destroy,admin'])->only(['destroy']);
    }

    // Hiển thị danh sách vai trò
    function index(): View
    {
        $roles = Role::all(); // Lấy tất cả các vai trò
        return view('admin.role.index', compact('roles')); // Truyền dữ liệu vào view
    }

    // Hiển thị giao diện tạo mới vai trò
    function create(): View
    {
        $premissions = Permission::all()->groupBy('group_name'); // Nhóm các quyền theo nhóm
        return view('admin.role.create', compact('premissions')); // Truyền dữ liệu vào view
    }

    // Lưu vai trò mới vào cơ sở dữ liệu
    function store(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'max:50', 'unique:permissions,name'] // Kiểm tra tính hợp lệ của dữ liệu
        ]);

        // Tạo mới vai trò
        $role = Role::create(['guard_name' => 'admin', 'name' => $request->role]);

        // Gán quyền cho vai trò
        $role->syncPermissions($request->permissions);

        toast(__('admin.Created Successfully'), 'success'); // Hiển thị thông báo
        return redirect()->route('admin.role.index'); // Quay lại danh sách vai trò
    }

    // Hiển thị giao diện chỉnh sửa vai trò
    function edit(string $id): View
    {
        $premissions = Permission::all()->groupBy('group_name'); // Lấy danh sách quyền
        $role = Role::findOrFail($id); // Tìm vai trò theo ID
        $rolesPermissions = $role->permissions->pluck('name')->toArray(); // Lấy danh sách quyền của vai trò
        return view('admin.role.edit', compact('premissions', 'role', 'rolesPermissions')); // Truyền dữ liệu vào view
    }

    // Cập nhật thông tin vai trò
    function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'max:50', 'unique:permissions,name'] // Kiểm tra tính hợp lệ của dữ liệu
        ]);

        // Tìm vai trò theo ID và cập nhật
        $role = Role::findOrFail($id);
        $role->update(['guard_name' => 'admin', 'name' => $request->role]);

        // Gán lại quyền cho vai trò
        $role->syncPermissions($request->permissions);

        toast(__('admin.Update Successfully'), 'success'); // Hiển thị thông báo
        return redirect()->route('admin.role.index'); // Quay lại danh sách vai trò
    }

    // Xóa vai trò
    function destory(string $id): Response
    {
        $role = Role::findOrFail($id); // Tìm vai trò theo ID

        // Không cho phép xóa vai trò Super Admin
        if ($role->name === 'Super Admin') {
            return response(['status' => 'error', 'message' => __('admin.Can\'t Delete the Super Admin')]);
        }

        $role->delete(); // Xóa vai trò
        return response(['status' => 'success', 'message' => __('admin.Deleted Successfully')]); // Trả về thông báo
    }
}

