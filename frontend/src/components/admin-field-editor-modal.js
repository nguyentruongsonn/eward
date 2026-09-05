import { el } from "./dom.js";
import { api } from "../api/client.js";

export function openFieldEditorModal({ field, onSaved }) {
  const isEdit = Boolean(field?.id);

  const overlay = el("div", {
    style: "position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem;",
  });

  const box = el("div", {
    class: "card",
    style: "background: #fff; width: 100%; max-width: 480px; display: flex; flex-direction: column; border-radius: 6px; overflow: hidden;",
  });

  const headerNode = el("div", {
    style: "padding: 1rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;",
  }, [
    el("h3", { style: "font-size: 16px; font-weight: 800; color: #004482; margin: 0;" },
      isEdit ? `Chỉnh sửa: ${field.name}` : "Thêm lĩnh vực mới"
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
    value: field?.name || "",
    placeholder: "Ví dụ: Hộ tịch, Đất đai, Xây dựng...",
    style: "width: 100%; padding: 0.5rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;",
  });

  const bodyNode = el("div", {
    style: "padding: 1.25rem 1.5rem; display: flex; flex-direction: column; gap: 0.85rem;",
  }, [
    el("div", {}, [
      el("label", { style: "display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 0.35rem;" }, "Tên lĩnh vực (*):"),
      nameInput,
    ]),
  ]);

  const submitBtn = el("button", {
    type: "button",
    class: "btn btn-primary",
    style: "font-weight: 700; height: 36px;",
    onClick: handleSave,
  }, isEdit ? "Lưu thay đổi" : "Tạo lĩnh vực");

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

    const name = nameInput.value.trim();
    if (!name) {
      modalError.textContent = "Vui lòng nhập tên lĩnh vực.";
      modalError.style.display = "block";
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = "Đang lưu...";

    try {
      if (isEdit) {
        await api.patch(`/admin/fields/${field.id}`, { name });
      } else {
        await api.post("/admin/fields", { name });
      }
      overlay.remove();
      if (onSaved) onSaved();
    } catch (err) {
      submitBtn.disabled = false;
      submitBtn.textContent = isEdit ? "Lưu thay đổi" : "Tạo lĩnh vực";
      const msg = err?.data?.errors?.message || err?.data?.message || err?.message || "Lỗi lưu lĩnh vực.";
      modalError.textContent = msg;
      modalError.style.display = "block";
    }
  }
}
