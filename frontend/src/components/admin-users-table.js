import { el } from "./dom.js";

export function getRoleBadgeColor(r = "") {
  if (r.includes("Quản trị")) return { bg: "#fef3c7", color: "#92400e" };
  if (r.includes("Lãnh đạo")) return { bg: "#f3e8ff", color: "#6b21a8" };
  if (r.includes("một cửa")) return { bg: "#eff6ff", color: "#1d4ed8" };
  if (r.includes("thụ lý")) return { bg: "#f0fdf4", color: "#15803d" };
  if (r.includes("Checkin")) return { bg: "#ecfeff", color: "#0e7490" };
  return { bg: "#f1f5f9", color: "#475569" };
}

export function createUsersTable({ items, currentUserId, onEdit, onDelete }) {
  if (!items.length) {
    return el("div", { style: "padding: 3rem; text-align: center; color: #94a3b8; font-size: 13.5px;" },
      "Không tìm thấy người dùng nào phù hợp."
    );
  }

  return el("table", {
    class: "table",
    style: "width: 100%; border-collapse: collapse; font-size: 13px; min-width: 760px;",
  }, [
    el("thead", {}, [
      el("tr", { style: "background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;" }, [
        el("th", { style: "padding: 0.75rem 1rem; font-weight: 700; color: #475569;" }, "Họ và tên"),
        el("th", { style: "padding: 0.75rem 1rem; font-weight: 700; color: #475569; width: 150px;" }, "Số điện thoại"),
        el("th", { style: "padding: 0.75rem 1rem; font-weight: 700; color: #475569; width: 140px;" }, "CCCD"),
        el("th", { style: "padding: 0.75rem 1rem; font-weight: 700; color: #475569; width: 160px;" }, "Vai trò"),
        el("th", { style: "padding: 0.75rem 1rem; text-align: right; font-weight: 700; color: #475569; width: 140px;" }, "Thao tác"),
      ]),
    ]),
    el("tbody", {},
      items.map((u) => {
        const isSelf = String(u.id) === String(currentUserId);
        const roleBadgeColor = getRoleBadgeColor(u.role);

        return el("tr", { style: "border-bottom: 1px solid #f1f5f9;" }, [
          el("td", { style: "padding: 0.75rem 1rem;" }, [
            el("div", { style: "font-weight: 700; color: #0f172a; margin-bottom: 0.2rem; display: flex; align-items: center; gap: 0.35rem;" }, [
              el("span", {}, u.full_name || "–"),
              isSelf ? el("span", { style: "font-size: 10px; background: #e0f2fe; color: #0284c7; padding: 0.1rem 0.35rem; border-radius: 3px; font-weight: 700;" }, "Bạn") : el("span"),
            ]),
            el("div", { style: "font-size: 12px; color: #64748b;" }, u.email || "–"),
          ]),
          el("td", { style: "padding: 0.75rem 1rem; color: #334155;" }, u.phone || "–"),
          el("td", { style: "padding: 0.75rem 1rem; font-family: monospace; font-size: 12px; color: #475569;" }, u.citizen_id || "–"),
          el("td", { style: "padding: 0.75rem 1rem;" }, [
            el("span", {
              style: `display: inline-block; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 11.5px; font-weight: 700; background: ${roleBadgeColor.bg}; color: ${roleBadgeColor.color};`,
            }, u.role || "–"),
          ]),
          el("td", { style: "padding: 0.75rem 1rem; text-align: right;" }, [
            el("div", { style: "display: inline-flex; gap: 0.35rem;" }, [
              el("button", {
                type: "button",
                class: "btn btn-secondary btn-sm",
                style: "border: 1px solid #cbd5e1; font-size: 11.5px; padding: 0.2rem 0.5rem;",
                onClick: () => onEdit(u),
              }, "Sửa"),
              el("button", {
                type: "button",
                class: "btn btn-danger btn-sm",
                style: isSelf
                  ? "border: 1px solid #e2e8f0; font-size: 11.5px; padding: 0.2rem 0.5rem; background: #f1f5f9; color: #94a3b8; cursor: not-allowed;"
                  : "border: 1px solid #fca5a5; font-size: 11.5px; padding: 0.2rem 0.5rem; background: #fee2e2; color: #dc2626;",
                disabled: isSelf,
                title: isSelf ? "Không thể tự xóa tài khoản của mình" : "Xóa người dùng",
                onClick: () => onDelete(u),
              }, "Xóa"),
            ]),
          ]),
        ]);
      })
    ),
  ]);
}
