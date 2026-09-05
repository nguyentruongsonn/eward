import { el } from "../components/dom.js";
import { api, getAuthToken, getStoredUser } from "../api/client.js";
import { normalizeStaffRole } from "../components/staff-nav.js";
import { getDefaultReportRange, validateReportRange } from "../components/report-dates.js";
import {
  createKpiCard,
  createStatusBreakdownSection,
  createProcedureBreakdownSection,
  createRevenueBreakdownSection,
} from "../components/staff-reports-sections.js";

export function renderStaffReportsPage({ navigate, searchParams }) {
  const container = el("div", {
    class: "staff-workspace-wrapper",
    style: "padding: 1.5rem 2rem; width: 100%; box-sizing: border-box;",
  });

  const token = getAuthToken();
  if (!token) {
    container.append(
      el("div", { class: "card", style: "padding: 3rem; text-align: center;" }, [
        el("h3", { style: "color: #b91c1c; font-weight: 800;" }, "YÊU CẦU ĐĂNG NHẬP"),
        el("p", { style: "color: #64748b; margin: 1rem 0;" }, "Vui lòng đăng nhập để xem báo cáo thống kê."),
        el("button", { class: "btn btn-primary", onClick: () => navigate("/dang-nhap") }, "Đăng nhập ngay"),
      ])
    );
    return container;
  }

  const user = getStoredUser();
  const normRole = normalizeStaffRole(user?.vaiTro || user?.role);
  if (normRole === "one-stop") {
    container.append(
      el("div", { class: "card", style: "padding: 3rem; text-align: center;" }, [
        el("h3", { style: "color: #b91c1c; font-weight: 800;" }, "KHÔNG CÓ QUYỀN TRUY CẬP"),
        el("p", { style: "color: #64748b; margin: 1rem 0;" }, "Cán bộ Một cửa không có quyền xem Báo cáo & Thống kê."),
        el("button", { class: "btn btn-primary", onClick: () => navigate("/") }, "Quay về Trang chủ"),
      ])
    );
    return container;
  }

  const defaultRange = getDefaultReportRange();
  let fromDate = searchParams?.get("from") || defaultRange.from;
  let toDate = searchParams?.get("to") || defaultRange.to;

  const errorBanner = el("div", {
    style: "display: none; padding: 0.75rem 1rem; border-radius: 4px; background: #fee2e2; border: 1px solid #f87171; color: #991b1b; font-size: 13px; margin-bottom: 1rem; font-weight: 600;",
  });

  const fromInput = el("input", {
    type: "date",
    class: "input",
    value: fromDate,
    style: "padding: 0.25rem 0.6rem; font-size: 12.5px; height: 32px; border: 1px solid #cbd5e1; border-radius: 4px; width: 135px;",
    onChange: (e) => { fromDate = e.target.value; },
  });

  const toInput = el("input", {
    type: "date",
    class: "input",
    value: toDate,
    style: "padding: 0.25rem 0.6rem; font-size: 12.5px; height: 32px; border: 1px solid #cbd5e1; border-radius: 4px; width: 135px;",
    onChange: (e) => { toDate = e.target.value; },
  });

  function setQuickRange(days) {
    const end = new Date();
    const start = new Date();
    start.setDate(start.getDate() - (days - 1));
    const pad = (n) => String(n).padStart(2, '0');
    fromDate = `${start.getFullYear()}-${pad(start.getMonth() + 1)}-${pad(start.getDate())}`;
    toDate = `${end.getFullYear()}-${pad(end.getMonth() + 1)}-${pad(end.getDate())}`;
    fromInput.value = fromDate;
    toInput.value = toDate;
    loadReports();
  }

  const filterBar = el("div", {
    class: "card",
    style: "padding: 0.75rem 1rem; margin-bottom: 1.25rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(15,23,42,0.03);",
  }, [
    el("div", { style: "display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;" }, [
      el("div", { style: "display: flex; align-items: center; gap: 0.35rem;" }, [
        el("label", { style: "font-size: 12px; font-weight: 600; color: #64748b;" }, "Từ:"),
        fromInput,
      ]),
      el("div", { style: "display: flex; align-items: center; gap: 0.35rem;" }, [
        el("label", { style: "font-size: 12px; font-weight: 600; color: #64748b;" }, "Đến:"),
        toInput,
      ]),
      el("button", {
        type: "button",
        class: "btn btn-primary btn-sm",
        style: "background: #004b87; color: #ffffff; height: 32px; font-weight: 700; padding: 0 0.85rem; border-radius: 4px;",
        onClick: () => loadReports(),
      }, "Xem báo cáo"),
    ]),
    el("div", { style: "display: flex; gap: 0.5rem;" }, [
      el("button", {
        type: "button",
        class: "btn btn-secondary btn-sm",
        style: "border: 1px solid #cbd5e1; font-size: 12px; height: 30px; font-weight: 600;",
        onClick: () => setQuickRange(7),
      }, "7 ngày gần nhất"),
      el("button", {
        type: "button",
        class: "btn btn-secondary btn-sm",
        style: "border: 1px solid #cbd5e1; font-size: 12px; height: 30px; font-weight: 600;",
        onClick: () => setQuickRange(30),
      }, "30 ngày gần nhất"),
      el("button", {
        type: "button",
        class: "btn btn-secondary btn-sm",
        style: "height: 30px; font-size: 12px; font-weight: 700; background: #004b87; color: #ffffff; border: 1px solid #004b87; border-radius: 4px; padding: 0 0.75rem;",
        onClick: () => loadReports(),
      }, "Làm mới ⟳"),
    ]),
  ]);

  const contentArea = el("div", { style: "display: flex; flex-direction: column; gap: 1.25rem;" });

  container.append(errorBanner, filterBar, contentArea);

  let cachedApps = null;
  let cachedRev = null;

  async function loadReports() {
    errorBanner.style.display = "none";
    errorBanner.textContent = "";

    const validation = validateReportRange(fromDate, toDate);
    if (!validation.valid) {
      errorBanner.textContent = validation.error;
      errorBanner.style.display = "block";
      return;
    }

    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set("from", fromDate);
    currentUrl.searchParams.set("to", toDate);
    window.history.replaceState({}, "", currentUrl.toString());

    if (!cachedApps && !cachedRev) {
      contentArea.innerHTML = "";
      contentArea.append(
        el("div", { style: "padding: 3rem; text-align: center; color: #64748b; font-size: 14px;" }, "Đang tổng hợp dữ liệu báo cáo...")
      );
    }

    try {
      const [appsRes, revRes] = await Promise.all([
        api.get(`/admin/reports/applications?from=${encodeURIComponent(fromDate)}&to=${encodeURIComponent(toDate)}`),
        api.get(`/admin/reports/revenue?from=${encodeURIComponent(fromDate)}&to=${encodeURIComponent(toDate)}`),
      ]);

      cachedApps = appsRes?.data || { total: 0, by_status: [], by_procedure: [] };
      cachedRev = revRes?.data || { transaction_count: 0, total_amount: 0, by_day: [] };

      renderReportContent(cachedApps, cachedRev);
    } catch (err) {
      const errMsg = err?.data?.errors?.message || err?.message || "Không thể tải báo cáo.";
      errorBanner.textContent = `Lỗi: ${errMsg}`;
      errorBanner.style.display = "block";

      if (!cachedApps && !cachedRev) {
        contentArea.innerHTML = "";
        contentArea.append(
          el("div", { class: "card", style: "padding: 2.5rem; text-align: center;" }, [
            el("p", { style: "color: #b91c1c; font-weight: 700;" }, errMsg),
            el("button", {
              class: "btn btn-secondary btn-sm",
              style: "margin-top: 0.75rem; border: 1px solid #cbd5e1;",
              onClick: () => loadReports(),
            }, "Thử lại"),
          ])
        );
      }
    }
  }

  function renderReportContent(apps, rev) {
    contentArea.innerHTML = "";

    const totalApps = apps.total || 0;
    const totalTx = rev.transaction_count || 0;
    const totalAmount = rev.total_amount || 0;

    const finishedStatus = (apps.by_status || []).find((s) => s.status_id === 10 || s.status_name?.toLowerCase().includes("trả kết quả"));
    const finishedCount = finishedStatus ? finishedStatus.total : 0;
    const finishPercent = totalApps > 0 ? Math.round((finishedCount / totalApps) * 100) : 0;

    const kpiRow = el("div", {
      style: "display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;",
    }, [
      createKpiCard("TỔNG SỐ HỒ SƠ TIẾP NHẬN", String(totalApps), "Hồ sơ trong kỳ", "#004482", "#eff6ff"),
      createKpiCard("HỒ SƠ ĐÃ HOÀN THÀNH", `${finishedCount} (${finishPercent}%)`, "Tỷ lệ trả kết quả", "#15803d", "#f0fdf4"),
      createKpiCard("GIAO DỊCH THU PHÍ", String(totalTx), "Giao dịch thành công", "#b45309", "#fffbeb"),
      createKpiCard("TỔNG DOANH THU LỆ PHÍ", Number(totalAmount).toLocaleString("vi-VN") + " đ", "Thu qua cổng thanh toán", "#4338ca", "#eef2ff"),
    ]);

    const statusCard = createStatusBreakdownSection(apps.by_status || [], totalApps);
    const procedureCard = createProcedureBreakdownSection(apps.by_procedure || [], totalApps);
    const revenueCard = createRevenueBreakdownSection(rev.by_day || []);

    contentArea.append(
      kpiRow,
      el("div", { style: "display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.25rem;" }, [
        statusCard,
        procedureCard,
      ]),
      revenueCard
    );
  }

  loadReports();
  return container;
}
