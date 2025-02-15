<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRoleUserStoreRequest;
use App\Http\Requests\AdminRoleUserUpdateRequest;
use App\Mail\RoleUserCreateMail;
use App\Models\Admin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class RoleUserController extends Controller
{
    // Constructor: Áp dụng middleware để kiểm tra quyền truy cập các hành động
    public function __construct()
    {
        $this->middleware(['permission:access management index,admin'])->only(['index']);
        $this->middleware(['permission:access management create,admin'])->only(['create', 'store']);
        $this->middleware(['permission:access management update,admin'])->only(['edit', 'update', 'handleTitle']);
        $this->middleware(['permission:access management destroy,admin'])->only(['destroy']);
    }
    
    /**
     * Hiển thị danh sách tất cả các Admin
     */
    public function index() : View
    {
        $admins = Admin::all(); // Lấy tất cả Admin
        return view('admin.role-user.index', compact('admins')); // Truyền dữ liệu vào view
    }

    /**
     * Hiển thị giao diện tạo mới Admin
     */
    public function create() : View
    {
        $roles = Role::all(); // Lấy tất cả vai trò (Role)
        return view('admin.role-user.create', compact('roles')); // Truyền dữ liệu vào view
    }

    /**
     * Lưu thông tin Admin mới vào cơ sở dữ liệu
     */
    public function store(AdminRoleUserStoreRequest $request) : RedirectResponse
    {
        try {
            // Tạo một đối tượng Admin mới
            $user = new Admin();
            $user->image = ''; // Mặc định ảnh rỗng
            $user->name = $request->name; // Gán tên
            $user->email = $request->email; // Gán email
            $user->password = bcrypt($request->password); // Mã hóa mật khẩu
            $user->status = 1; // Kích hoạt trạng thái
            $user->save(); // Lưu Admin mới

            // Gán vai trò (Role) cho Admin
            $user->assignRole($request->role);

            // Gửi email thông báo cho Admin mới
            Mail::to($request->email)->send(new RoleUserCreateMail($request->email, $request->password));

            toast(__('admin.Created Successfully!'), 'success'); // Hiển thị thông báo thành công

            return redirect()->route('admin.role-users.index'); // Chuyển hướng về danh sách Admin
        } catch (\Throwable $th) {
            throw $th; // Xử lý lỗi (nếu có)
        }
    }

    /**
     * Hiển thị giao diện chỉnh sửa thông tin Admin
     */
    public function edit(string $id) : View
    {
        $user = Admin::findOrFail($id); // Tìm Admin theo ID
        $roles = Role::all(); // Lấy tất cả vai trò
        return view('admin.role-user.edit', compact('user', 'roles')); // Truyền dữ liệu vào view
    }

    /**
     * Cập nhật thông tin Admin
     */
    public function update(AdminRoleUserUpdateRequest $request, string $id)
    {
        // Kiểm tra và validate mật khẩu (nếu có thay đổi)
        if ($request->has('password') && !empty($request->password)) {
            $request->validate([
                'password' => ['confirmed', 'min:6']
            ]);
        }

        // Tìm Admin theo ID và cập nhật thông tin
        $user = Admin::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->has('password') && !empty($request->password)) {
            $user->password = bcrypt($request->password); // Mã hóa mật khẩu mới
        }

        $user->save(); // Lưu thông tin

        // Gán lại vai trò (Role) cho Admin
        $user->syncRoles($request->role);

        toast(__('admin.Update Successfully!'), 'success'); // Hiển thị thông báo thành công

        return redirect()->route('admin.role-users.index'); // Chuyển hướng về danh sách Admin
    }

    /**
     * Xóa Admin khỏi hệ thống
     */
    public function destroy(string $id)
    {
        $user = Admin::findOrFail($id); // Tìm Admin theo ID

        // Không cho phép xóa tài khoản Super Admin
        if ($user->getRoleNames()->first() === 'Super Admin') {
            return response(['status' => 'error', 'message' => __('admin.Can\'t Delete the Super User')]);
        }

        $user->delete(); // Xóa Admin

        return response(['status' => 'success', 'message' => __('admin.Deleted Successfully')]); // Trả về thông báo
    }
}
