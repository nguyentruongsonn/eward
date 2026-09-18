import { el } from "./dom.js";

export function createStaffTable({ items, onEdit, onDelete }) {
  if (!items.length) {
    return el("div", { style: "padding: 3rem; text-align: center; color: #94a3b8; font-size: 13.5px;" },
      "Không tìm thấy cán bộ nào phù hợp."
    );
  }

  return el("table", {
    class: "table",
    style: "width: 100%; border-collapse: collapse; font-size: 13px; min-width: 760px;",
  }, [
    el("thead", {}, [
      el("tr", { style: "background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;" }, [
        el("th", { style: "padding: 0.75rem 1rem; font-weight: 700; color: #475569;" }, "Cán bộ"),
        el("th", { style: "padding: 0.75rem 1rem; font-weight: 700; color: #475569; width: 140px;" }, "CCCD"),
        el("th", { style: "padding: 0.75rem 1rem; font-weight: 700; color: #475569; width: 160px;" }, "Vai trò"),
        el("th", { style: "padding: 0.75rem 1rem; font-weight: 700; color: #475569; width: 160px;" }, "Phân công quầy"),
        el("th", { style: "padding: 0.75rem 1rem; text-align: right; font-weight: 700; color: #475569; width: 140px;" }, "Thao tác"),
      ]),
    ]),
    el("tbody", {},
      items.map((staff) => {
        const isOneStop = staff.vaiTro?.includes("một cửa");
        const roleBg = isOneStop ? "#eff6ff" : "#f0fdf4";
        const roleColor = isOneStop ? "#1d4ed8" : "#15803d";

        return el("tr", { style: "border-bottom: 1px solid #f1f5f9;" }, [
          el("td", { style: "padding: 0.75rem 1rem;" }, [
            el("div", { style: "font-weight: 700; color: #0f172a; margin-bottom: 0.2rem;" }, staff.hoTen),
            el("div", { style: "font-size: 12px; color: #64748b;" }, `${staff.email}${staff.soDienThoai ? ` • ${staff.soDienThoai}` : ''}`),
          ]),
          el("td", { style: "padding: 0.75rem 1rem; font-family: monospace; font-size: 12.5px; color: #334155;" }, staff.maCCCD || "–"),
          el("td", { style: "padding: 0.75rem 1rem;" }, [
            el("span", {
              style: `display: inline-block; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 11.5px; font-weight: 700; background: ${roleBg}; color: ${roleColor};`,
            }, staff.vaiTro),
          ]),
          el("td", { style: "padding: 0.75rem 1rem;" }, [
            staff.tenQuayLamViec
              ? el("span", {
                  style: "display: inline-block; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 11.5px; font-weight: 700; background: #fef3c7; color: #92400e; border: 1px solid #fde68a;",
                }, staff.tenQuayLamViec)
              : el("span", { style: "color: #94a3b8; font-style: italic; font-size: 12px;" }, "Chưa gán quầy"),
          ]),
          el("td", { style: "padding: 0.75rem 1rem; text-align: right;" }, [
            el("div", { style: "display: inline-flex; gap: 0.35rem;" }, [
              el("button", {
                type: "button",
                class: "btn btn-secondary btn-sm",
                style: "border: 1px solid #cbd5e1; font-size: 11.5px; padding: 0.2rem 0.5rem;",
                onClick: () => onEdit(staff),
              }, "Sửa"),
              el("button", {
                type: "button",
                class: "btn btn-danger btn-sm",
                style: "border: 1px solid #fca5a5; font-size: 11.5px; padding: 0.2rem 0.5rem; background: #fee2e2; color: #dc2626;",
                onClick: () => onDelete(staff),
              }, "Xóa"),
            ]),
          ]),
        ]);
      })
    ),
  ]);
}
