import { el } from "../components/dom.js";
import { api, getAuthToken, getStoredUser } from "../api/client.js";
import { normalizeStaffRole } from "../components/staff-nav.js";
import { renderDashboardCharts } from "../components/staff-dashboard-charts.js";
import { createDashboardFilterBar } from "../components/staff-dashboard-filter.js";
import { getDefaultReportRange } from "../components/report-dates.js";
import { getPreviousPeriodRange } from "../components/dashboard-comparison.js";

export function renderStaffDashboardPage({ navigate }) {
  const container = el("div", {
    class: "staff-workspace-wrapper",
    style: "padding: 1.5rem 2rem; width: 100%; box-sizing: border-box;",
  });

  const token = getAuthToken();
  const user = getStoredUser();

  if (!token) {
    container.append(
      el("div", { class: "card", style: "padding: 3rem; text-align: center;" }, [
        el("h3", { style: "color: #b91c1c; font-weight: 800;" }, "YÊU CẦU ĐĂNG NHẬP"),
        el("p", { style: "color: #64748b; margin: 1rem 0;" }, "Vui lòng đăng nhập để truy cập Bàn làm việc."),
        el("button", { class: "btn btn-primary", onClick: () => navigate("/dang-nhap") }, "Đăng nhập ngay"),
      ])
    );
    return container;
  }

  const roleName = user?.role || user?.vaiTro || "Cán bộ giải quyết";
  const normRole = normalizeStaffRole(user?.vaiTro || user?.role);
  if (normRole === "one-stop") {
    container.append(
      el("div", { class: "card", style: "padding: 3rem; text-align: center;" }, [
        el("h3", { style: "color: #b91c1c; font-weight: 800;" }, "KHÔNG CÓ QUYỀN TRUY CẬP"),
        el("p", { style: "color: #64748b; margin: 1rem 0;" }, "Cán bộ Một cửa không có quyền xem Bảng điều hành."),
        el("button", { class: "btn btn-primary", onClick: () => navigate("/") }, "Quay về Trang chủ"),
      ])
    );
    return container;
  }

  const defRange = getDefaultReportRange();
  let fromDate = defRange.from;
  let toDate = defRange.to;
  let currentPreset = "30d";

  const filterBar = createDashboardFilterBar({
    initialFrom: fromDate,
    initialTo: toDate,
    onFilterChange: ({ from, to, preset }) => {
      fromDate = from;
      toDate = to;
      currentPreset = preset || "";
      loadDashboard();
    },
    onRefresh: () => loadDashboard(),
  });

  const contentArea = el("div", { style: "display: flex; flex-direction: column; gap: 1.25rem; margin-top: 1rem;" });

  async function loadDashboard() {
    contentArea.replaceChildren(
      el("div", { style: "text-align: center; padding: 3rem; color: #64748b;" }, "Đang tải dữ liệu bảng điều hành...")
    );

    try {
      const prevRange = getPreviousPeriodRange(fromDate, toDate, currentPreset);
      const reportUrl = `/admin/reports/applications?from=${encodeURIComponent(fromDate)}&to=${encodeURIComponent(toDate)}`;
      const prevReportUrl = `/admin/reports/applications?from=${encodeURIComponent(prevRange.from)}&to=${encodeURIComponent(prevRange.to)}`;

      const [dashRes, reportRes, prevReportRes] = await Promise.all([
        api.get("/admin/dashboard?limit=10"),
        api.get(reportUrl).catch(() => null),
        api.get(prevReportUrl).catch(() => null),
      ]);
      renderStaffView(dashRes?.data || {}, reportRes?.data || null, prevReportRes?.data || null, prevRange);
    } catch (err) {
      contentArea.replaceChildren(
        el("div", { class: "card", style: "padding: 2rem; color: #b91c1c; text-align: center;" },
          err.status === 403 ? "Bạn không có quyền truy cập vào bảng điều hành cán bộ." : `Không thể tải dữ liệu: ${err.message}`
        )
      );
    }
  }

  function renderStaffView(dashData, reportData, prevReportData, periodInfo) {
    const counterMap = {};
    let totalCount = 0;

    if (Array.isArray(dashData.counters)) {
      dashData.counters.forEach(c => {
        const id = c.status_id || Number(String(c.key).replace('status_', ''));
        const val = Number(c.value) || 0;
        if (id) counterMap[id] = (counterMap[id] || 0) + val;
        counterMap[c.key] = (counterMap[c.key] || 0) + val;
        totalCount += val;
      });
    } else if (dashData.counters && typeof dashData.counters === 'object') {
      Object.assign(counterMap, dashData.counters);
      totalCount = dashData.counters.total ?? Object.values(dashData.counters).reduce((a, b) => a + (Number(b) || 0), 0);
    }

    if (reportData?.total && reportData.total > totalCount) {
      totalCount = reportData.total;
    }
    if (Array.isArray(reportData?.by_status)) {
      reportData.by_status.forEach(s => {
        if (!counterMap[s.status_id]) counterMap[s.status_id] = s.total || 0;
      });
    }

    const normRole = normalizeStaffRole(roleName);
    const pendingReception = counterMap[1] || 0;
    const processing = (counterMap[4] || 0) + (counterMap[2] || 0);
    const supplement = counterMap[5] || 0;
    const rework = counterMap[12] || 0;
    const completed = counterMap[9] || 0;
    const delivered = counterMap[10] || 0;

    let counterEntries = [];
    if (normRole === 'leader') {
      counterEntries = [
        { label: "Tổng hồ sơ phụ trách", value: totalCount, path: "/can-bo/ho-so", color: "#004b87" },
        { label: "Chờ phê duyệt", value: processing, path: "/can-bo/ho-so?status=4", color: "#d97706" },
        { label: "Đã phê duyệt", value: completed, path: "/can-bo/ho-so?status=9", color: "#16a34a" },
        { label: "Yêu cầu xử lý lại", value: rework, path: "/can-bo/ho-so?status=12", color: "#dc2626" },
      ];
    } else if (normRole === 'case-officer') {
      counterEntries = [
        { label: "Tổng hồ sơ thụ lý", value: totalCount, path: "/can-bo/ho-so", color: "#004b87" },
        { label: "Đang thụ lý", value: processing, path: "/can-bo/ho-so?status=2", color: "#0284c7" },
        { label: "Yêu cầu bổ sung", value: supplement, path: "/can-bo/ho-so?status=5", color: "#ea580c" },
        { label: "Yêu cầu xử lý lại", value: rework, path: "/can-bo/ho-so?status=12", color: "#dc2626" },
        { label: "Đã hoàn thành", value: completed, path: "/can-bo/ho-so?status=9", color: "#16a34a" },
      ];
    } else if (normRole === 'one-stop') {
      counterEntries = [
        { label: "Tổng số hồ sơ", value: totalCount, path: "/can-bo/ho-so", color: "#004b87" },
        { label: "Chờ tiếp nhận", value: pendingReception, path: "/can-bo/ho-so?status=1", color: "#d97706" },
        { label: "Đang thụ lý", value: processing, path: "/can-bo/ho-so?status=2", color: "#0284c7" },
        { label: "Chờ trả kết quả", value: completed, path: "/can-bo/ho-so?status=9", color: "#16a34a" },
        { label: "Đã trả kết quả", value: delivered, path: "/can-bo/ho-so?status=10", color: "#475569" },
      ];
    } else {
      counterEntries = [
        { label: "Tổng số hồ sơ", value: totalCount, path: "/can-bo/ho-so", color: "#004b87" },
        { label: "Chờ tiếp nhận", value: pendingReception, path: "/can-bo/ho-so?status=1", color: "#d97706" },
        { label: "Đang thụ lý", value: processing, path: "/can-bo/ho-so?status=2", color: "#0284c7" },
        { label: "Yêu cầu bổ sung", value: supplement, path: "/can-bo/ho-so?status=5", color: "#ea580c" },
        { label: "Yêu cầu xử lý lại", value: rework, path: "/can-bo/ho-so?status=12", color: "#dc2626" },
        { label: "Đã xử lý xong", value: completed + delivered, path: "/can-bo/ho-so?status=9", color: "#16a34a" },
      ];
    }

    const countersGrid = el("div", {
      style: "display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 0.85rem;",
    }, counterEntries.map(c => el("div", {
      class: "card",
      style: `padding: 1.1rem 1.25rem; background: #ffffff; border: 1px solid #d0d7de; border-left: 4px solid ${c.color}; border-radius: 6px; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 1px 3px rgba(15,23,42,0.04);`,
      onClick: () => navigate(c.path),
      onmouseenter: (e) => { e.currentTarget.style.transform = 'translateY(-2px)'; e.currentTarget.style.boxShadow = '0 4px 10px rgba(15,23,42,0.08)'; },
      onmouseleave: (e) => { e.currentTarget.style.transform = 'none'; e.currentTarget.style.boxShadow = '0 1px 3px rgba(15,23,42,0.04)'; },
    }, [
      el("div", { style: "font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.3px;" }, c.label),
      el("div", { class: "numeric-data", style: `font-size: 26px; font-weight: 800; color: ${c.color}; margin-top: 0.35rem;` }, String(c.value)),
    ])));

    const statusList = (reportData?.by_status?.length)
      ? reportData.by_status
      : (Array.isArray(dashData.counters) ? dashData.counters : []);

    const procedureList = reportData?.by_procedure || [];

    const chartsSection = renderDashboardCharts({
      statusList,
      procedureList,
      totalCount,
      prevReportData,
      periodInfo,
      onNavigate: navigate,
    });

    contentArea.replaceChildren(countersGrid, chartsSection);
  }

  container.append(filterBar, contentArea);
  loadDashboard();
  return container;
}
