/**
 * Role-aware Staff Navigation Model
 */

export function normalizeStaffRole(value) {
  if (!value) return null;
  const str = String(value).trim().toLowerCase();
  if (str.includes("quản trị") || str.includes("admin")) return "administrator";
  if (str.includes("một cửa") || str.includes("one-stop")) return "one-stop";
  if (str.includes("thụ lý") || str.includes("case")) return "case-officer";
  if (str.includes("lãnh đạo") || str.includes("leader")) return "leader";
  if (str.includes("checkin")) return "checkin";
  return null;
}

const rawGroups = [
  {
    id: "overview",
    title: "I. TỔNG QUAN",
    items: [
      {
        path: "/can-bo",
        label: "Bảng điều hành",
        roles: ["administrator", "case-officer", "leader", "checkin"],
      },
    ],
  },
  {
    id: "applications",
    title: "II. HỒ SƠ",
    items: [
      {
        path: "/can-bo/ho-so",
        label: "Tất cả hồ sơ",
        roles: ["administrator", "one-stop", "case-officer", "leader"],
      },
      {
        path: "/can-bo/ho-so?status=1&payment_status=paid",
        label: "Chờ tiếp nhận",
        roles: ["administrator", "one-stop"],
      },
      {
        path: "/can-bo/ho-so?status=13",
        label: "Chờ thanh toán",
        roles: ["administrator", "one-stop"],
      },
      {
        path: "/can-bo/ho-so?status=2",
        label: "Đang thụ lý",
        roles: ["administrator", "case-officer"],
      },
      {
        path: "/can-bo/ho-so?status=12",
        label: "Yêu cầu xử lý lại",
        roles: ["administrator", "case-officer", "leader"],
      },
      {
        path: "/can-bo/ho-so?status=5",
        label: "Chờ bổ sung",
        roles: ["administrator", "case-officer", "one-stop"],
      },
      {
        path: "/can-bo/ho-so?status=4",
        label: "Chờ phê duyệt",
        roles: ["administrator", "leader", "case-officer"],
      },
      {
        path: "/can-bo/ho-so?status=9",
        label: "Đã phê duyệt",
        roles: ["leader"],
      },
      {
        path: "/can-bo/ho-so?status=9",
        label: "Chờ trả kết quả",
        roles: ["administrator", "one-stop"],
      },
      {
        path: "/can-bo/ho-so?status=10",
        label: "Đã trả kết quả",
        roles: ["administrator", "one-stop"],
      },
    ],
  },
  {
    id: "reports",
    title: "III. BÁO CÁO & THỐNG KÊ",
    items: [
      {
        path: "/can-bo/bao-cao",
        label: "Báo cáo tiến độ & doanh thu",
        roles: ["administrator", "case-officer", "leader"],
      },
    ],
  },
  {
    id: "catalog",
    title: "IV. DANH MỤC THỦ TỤC",
    items: [
      {
        path: "/can-bo/danh-muc-thu-tuc",
        label: "Danh mục TTHC công khai",
        roles: ["administrator", "one-stop", "case-officer", "leader"],
      },
    ],
  },
  {
    id: "administration",
    title: "V. QUẢN TRỊ HỆ THỐNG",
    items: [
      {
        path: "/can-bo/quan-tri/nguoi-dung",
        label: "Quản lý người dùng",
        roles: ["administrator"],
      },
      {
        path: "/can-bo/quan-tri/can-bo",
        label: "Phân công cán bộ & quầy",
        roles: ["administrator"],
      },
      {
        path: "/can-bo/quan-tri/linh-vuc",
        label: "Danh mục lĩnh vực",
        roles: ["administrator"],
      },
    ],
  },
];

export function getStaffNavigation(rawRole, currentPath = "") {
  const normalized = normalizeStaffRole(rawRole);
  if (!normalized) return [];

  const visibleGroups = [];

  for (const group of rawGroups) {
    const matchingItems = group.items.filter((item) =>
      item.roles.includes(normalized)
    );

    if (matchingItems.length > 0) {
      visibleGroups.push({
        id: group.id,
        title: group.title,
        items: matchingItems,
      });
    }
  }

  return visibleGroups;
}
