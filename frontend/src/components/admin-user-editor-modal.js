import { el } from "./dom.js";
import { api } from "../api/client.js";

const availableRoles = [
  "Quản trị viên",
  "Cán bộ tiếp nhận",
  "Cán bộ thụ lý",
  "Lãnh đạo",
  "Checkin",
  "Công dân",
];

export function openUserEditorModal({ user: u, onSaved }) {
  const isEdit = Boolean(u?.id);

  const overlay = el("div", {
    style: "position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem;",
  });

  const box = el("div", {
    class: "card",
    style: "background: #fff; width: 100%; max-width: 540px; max-height: 90vh; display: flex; flex-direction: column; border-radius: 6px; overflow: hidden;",
  });

  const headerNode = el("div", {
    style: "padding: 1rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;",
  }, [
    el("h3", { style: "font-size: 16px; font-weight: 800; color: #004482; margin: 0;" },
      isEdit ? `Cập nhật người dùng: ${u.full_name}` : "Tạo tài khoản người dùng mới"
    ),
    el("button", {
      type: "button",
      style: "background: none; border: none; cursor: pointer; color: #94a3b8; display: flex; align-items: center;",
      onClick: () => overlay.remove(),
    }, [el("span", { class: "material-symbols-outlined", style: "font-size: 20px;" }, "close")]),
  ]);

  const modalError = el("div", {
    style: "display: none; margin: 1rem 1.5rem 0; padding: 0.75rem 1rem; border-radius: 4px; background: #fee2e2; border: 1px solid #f87171; color: #991b1b; font-size: 13px; font-weight: 600;",
  });

  const nameInput = el("input", {
    type: "text",
    class: "input",
    value: u?.full_name || "",
    style: "width: 100%; padding: 0.5rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;",
  });

  const emailInput = el("input", {
    type: "email",
    class: "input",
    value: u?.email || "",
    style: "width: 100%; padding: 0.5rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;",
  });

  const phoneInput = el("input", {
    type: "text",
    class: "input",
    value: u?.phone || "",
    placeholder: "10 số điện thoại",
    style: "width: 100%; padding: 0.5rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;",
  });

  const cccdInput = el("input", {
    type: "text",
    class: "input",
    value: u?.citizen_id || "",
    placeholder: "12 số CCCD",
    style: "width: 100%; padding: 0.5rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;",
  });

  const passwordInput = el("input", {
    type: "password",
    class: "input",
    placeholder: isEdit ? "Để trống nếu không đổi mật khẩu" : "Mật khẩu tối thiểu 12 ký tự",
    style: "width: 100%; padding: 0.5rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;",
  });

  const roleSelectInput = el("select", {
    class: "input",
    style: "width: 100%; padding: 0.5rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;",
  }, availableRoles.map((r) => el("option", { value: r, selected: r === u?.role }, r)));

  const bodyNode = el("div", {
    style: "padding: 1.25rem 1.5rem; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 0.85rem;",
  }, [
    el("div", {}, [
      el("label", { style: "display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 0.35rem;" }, "Họ và tên (*):"),
      nameInput,
    ]),
    el("div", { style: "display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;" }, [
      el("div", {}, [
        el("label", { style: "display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 0.35rem;" }, "Email (*):"),
        emailInput,
      ]),
      el("div", {}, [
        el("label", { style: "display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 0.35rem;" }, "Số điện thoại (*):"),
        phoneInput,
      ]),
    ]),
    el("div", { style: "display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;" }, [
      el("div", {}, [
        el("label", { style: "display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 0.35rem;" }, "Số CCCD:"),
        cccdInput,
      ]),
      el("div", {}, [
        el("label", { style: "display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 0.35rem;" }, "Vai trò hệ thống (*):"),
        roleSelectInput,
      ]),
    ]),
    el("div", {}, [
      el("label", { style: "display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 0.35rem;" },
        isEdit ? "Mật khẩu mới (Tùy chọn):" : "Mật khẩu đăng nhập (*):"
      ),
      passwordInput,
    ]),
  ]);

  const submitBtn = el("button", {
    type: "button",
    class: "btn btn-primary",
    style: "font-weight: 700; height: 36px;",
    onClick: handleSave,
  }, isEdit ? "Lưu thay đổi" : "Tạo người dùng");

  const footerNode = el("div", {
    style: "padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.5rem;",
  }, [
    el("button", {
      type: "button",
      class: "btn btn-secondary",
      style: "border: 1px solid #cbd5e1; height: 36px;",
      onClick: () => overlay.remove(),
    }, "Hủy bỏ"),
    submitBtn,
  ]);

  box.append(headerNode, modalError, bodyNode, footerNode);
  overlay.append(box);
  document.body.append(overlay);

  async function handleSave() {
    modalError.style.display = "none";
    modalError.textContent = "";

    const fullName = nameInput.value.trim();
    const email = emailInput.value.trim();
    const phone = phoneInput.value.trim();
    const citizenId = cccdInput.value.trim();
    const roleVal = roleSelectInput.value;
    const pwd = passwordInput.value;

    if (!fullName || !email || !phone || (!isEdit && !pwd)) {
      modalError.textContent = "Vui lòng nhập đầy đủ các trường thông tin bắt buộc (*).";
      modalError.style.display = "block";
      return;
    }

    const payload = {
      full_name: fullName,
      email,
      phone,
      citizen_id: citizenId || null,
      role: roleVal,
    };

    if (pwd) {
      payload.password = pwd;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = "Đang lưu...";

    try {
      if (isEdit) {
        await api.patch(`/admin/users/${u.id}`, payload);
      } else {
        await api.post("/admin/users", payload);
      }
      overlay.remove();
      if (onSaved) onSaved();
    } catch (err) {
      submitBtn.disabled = false;
      submitBtn.textContent = isEdit ? "Lưu thay đổi" : "Tạo người dùng";
      const msg = err?.data?.errors?.message || err?.data?.message || err?.message || "Lỗi lưu tài khoản người dùng.";
      modalError.textContent = msg;
      modalError.style.display = "block";
    }
  }
}
