import { el } from "./dom.js";
import { api } from "../api/client.js";

const inputStyle = "width: 100%; padding: 0.5rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;";
const labelStyle = "display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 0.35rem;";

function formGroup(labelText, control) {
  return el("div", {}, [el("label", { style: labelStyle }, labelText), control]);
}

export function openProcedureEditorModal({ procedure = null, fields = [], onSave, onDelete }) {
  const isEdit = Boolean(procedure?.id);

  const overlay = el("div", {
    class: "modal-overlay",
    style: "position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem;",
  });

  const modalBox = el("div", {
    class: "modal-box card",
    style: "background: #fff; width: 100%; max-width: 720px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border-radius: 6px; overflow: hidden;",
  });

  const header = el("div", {
    style: "padding: 1rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;",
  }, [
    el("div", {}, [
      el("div", { style: "font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;" }, "QUẢN TRỊ DANH MỤC THỦ TỤC"),
      el("h2", { style: "font-size: 16px; font-weight: 800; color: #004482; margin: 0.2rem 0 0 0;" },
        isEdit ? `Cập nhật thủ tục: ${procedure.name}` : "Thêm mới thủ tục hành chính"
      ),
    ]),
    el("button", {
      type: "button",
      style: "background: none; border: none; font-size: 20px; cursor: pointer; color: #94a3b8; font-weight: 700; line-height: 1;",
      onClick: () => overlay.remove(),
    }, "✕"),
  ]);

  const errorBanner = el("div", {
    style: "display: none; margin: 1rem 1.5rem 0; padding: 0.75rem 1rem; border-radius: 4px; background: #fee2e2; border: 1px solid #f87171; color: #991b1b; font-size: 13px; font-weight: 600;",
  });

  const nameInput = el("input", { type: "text", class: "input", required: true, value: procedure?.name || "", style: inputStyle });

  const fieldSelect = el("select", { class: "input", style: inputStyle }, [
    el("option", { value: "" }, "-- Chọn lĩnh vực --"),
    ...fields.map((f) => el("option", {
      value: String(f.id),
      selected: String(f.id) === String(procedure?.field_id),
    }, f.name)),
  ]);

  const statusSelect = el("select", { class: "input", style: inputStyle }, [
    el("option", { value: "Công khai", selected: procedure?.status === "Công khai" || !procedure }, "Công khai"),
    el("option", { value: "Chờ công khai", selected: procedure?.status === "Chờ công khai" }, "Chờ công khai"),
    el("option", { value: "Bãi bỏ", selected: procedure?.status === "Bãi bỏ" }, "Bãi bỏ"),
  ]);

  const targetInput = el("input", { type: "text", class: "input", value: procedure?.target || "Công dân, tổ chức", style: inputStyle });
  const agencyInput = el("input", { type: "text", class: "input", value: procedure?.agency || "Ủy ban nhân dân cấp xã/phường", style: inputStyle });
  const resultInput = el("input", { type: "text", class: "input", value: procedure?.result || "Văn bản xác nhận / Kết quả giải quyết TTHC", style: inputStyle });

  const instructionsText = el("textarea", {
    class: "input", rows: "3", style: `${inputStyle} font-family: inherit;`,
  }, procedure?.instructions || "Nộp hồ sơ trực tuyến hoặc nộp trực tiếp tại Bộ phận Một cửa.");

  const requirementsText = el("textarea", {
    class: "input", rows: "2", style: `${inputStyle} font-family: inherit;`,
  }, procedure?.requirements || "Không có yêu cầu, điều kiện đặc thù.");

  const legalBasisText = el("textarea", {
    class: "input", rows: "2", style: `${inputStyle} font-family: inherit;`,
  }, procedure?.legal_basis || "Quy định pháp luật hiện hành liên quan.");

  const body = el("div", {
    style: "padding: 1.25rem 1.5rem; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 1rem;",
  }, [
    formGroup("Tên thủ tục hành chính (*)", nameInput),
    el("div", { style: "display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" }, [
      formGroup("Lĩnh vực quản lý (*)", fieldSelect),
      formGroup("Trạng thái công khai", statusSelect),
    ]),
    el("div", { style: "display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" }, [
      formGroup("Đối tượng thực hiện (*)", targetInput),
      formGroup("Cơ quan giải quyết (*)", agencyInput),
    ]),
    formGroup("Kết quả giải quyết (*)", resultInput),
    formGroup("Trình tự thực hiện (*)", instructionsText),
    formGroup("Yêu cầu, điều kiện thực hiện (*)", requirementsText),
    formGroup("Căn cứ pháp lý (*)", legalBasisText),
  ]);

  const submitBtn = el("button", {
    type: "button",
    class: "btn btn-primary",
    style: "font-weight: 700; height: 36px; padding: 0 1.25rem;",
    onClick: handleSubmit,
  }, isEdit ? "Lưu thay đổi" : "Tạo thủ tục");

  const footerActions = el("div", {
    style: "padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;",
  }, [
    el("div", {}, [
      isEdit && onDelete
        ? el("button", {
            type: "button",
            class: "btn btn-danger btn-sm",
            style: "background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; font-weight: 700;",
            onClick: handleDelete,
          }, "Xóa thủ tục")
        : el("span"),
    ]),
    el("div", { style: "display: flex; gap: 0.5rem;" }, [
      el("button", {
        type: "button",
        class: "btn btn-secondary",
        style: "border: 1px solid #cbd5e1; height: 36px;",
        onClick: () => overlay.remove(),
      }, "Hủy bỏ"),
      submitBtn,
    ]),
  ]);

  modalBox.append(header, errorBanner, body, footerActions);
  overlay.append(modalBox);
  document.body.append(overlay);

  async function handleSubmit() {
    errorBanner.style.display = "none";
    errorBanner.textContent = "";

    const name = nameInput.value.trim();
    const fieldId = parseInt(fieldSelect.value, 10);
    const status = statusSelect.value;
    const target = targetInput.value.trim();
    const agency = agencyInput.value.trim();
    const result = resultInput.value.trim();
    const instructions = instructionsText.value.trim();
    const requirements = requirementsText.value.trim();
    const legalBasis = legalBasisText.value.trim();

    if (!name) return showError("Vui lòng nhập tên thủ tục hành chính.");
    if (!fieldId) return showError("Vui lòng chọn lĩnh vực.");
    if (!target || !agency || !result || !instructions || !requirements || !legalBasis) {
      return showError("Vui lòng điền đầy đủ các trường thông tin bắt buộc (*).");
    }

    const payload = { name, field_id: fieldId, status, target, agency, result, instructions, requirements, legal_basis: legalBasis };
    submitBtn.disabled = true;
    submitBtn.textContent = "Đang xử lý...";

    try {
      let res;
      if (isEdit) {
        res = await api.patch(`/admin/procedures/${procedure.id}`, payload);
      } else {
        res = await api.post("/admin/procedures", payload);
      }
      overlay.remove();
      if (typeof onSave === "function") onSave(res?.data);
    } catch (err) {
      submitBtn.disabled = false;
      submitBtn.textContent = isEdit ? "Lưu thay đổi" : "Tạo thủ tục";
      const msg = err?.data?.errors?.message || err?.data?.message || err?.message || "Lỗi lưu thủ tục.";
      showError(msg);
    }
  }

  async function handleDelete() {
    if (!confirm(`Bạn có chắc chắn muốn xóa thủ tục "${procedure.name}" không?`)) return;

    try {
      await api.delete(`/admin/procedures/${procedure.id}`);
      overlay.remove();
      if (typeof onDelete === "function") onDelete(procedure.id);
    } catch (err) {
      const msg = err?.data?.errors?.message || err?.data?.message || err?.message || "Không thể xóa thủ tục.";
      showError(`Không thể xóa: ${msg}`);
    }
  }

  function showError(msg) {
    errorBanner.textContent = msg;
    errorBanner.style.display = "block";
    body.scrollTop = 0;
  }
}
