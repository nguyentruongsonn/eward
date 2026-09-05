/**
 * Staff View Resolution Helper
 */
export function resolveStaffView(pathname = "", searchParams = new URLSearchParams()) {
  const cleanPath = pathname.replace(/\/+$/, "") || "/";

  if (cleanPath === "/can-bo") {
    if (searchParams.has("status")) {
      return "queue";
    }
    return "dashboard";
  }

  if (cleanPath === "/can-bo/ho-so") {
    return "queue";
  }

  if (cleanPath.startsWith("/can-bo/ho-so/")) {
    return "detail";
  }

  if (cleanPath === "/can-bo/tra-cuu") {
    return "tracking";
  }

  if (cleanPath === "/can-bo/bao-cao") {
    return "reports";
  }

  if (cleanPath === "/can-bo/danh-muc-thu-tuc") {
    return "catalog";
  }

  if (cleanPath === "/can-bo/quan-tri/nguoi-dung") {
    return "admin-users";
  }

  if (cleanPath === "/can-bo/quan-tri/can-bo") {
    return "admin-staff";
  }

  if (cleanPath === "/can-bo/quan-tri/linh-vuc") {
    return "admin-fields";
  }

  return "unknown";
}

export function canAccessStaffRoute(pathname = "", role = null) {
  if (!role) return false;
  const cleanPath = pathname.replace(/\/+$/, "") || "/";

  if (role === "administrator") return true;

  if (cleanPath === "/can-bo") {
    return ["case-officer", "leader", "checkin"].includes(role);
  }

  if (cleanPath === "/can-bo/ho-so" || cleanPath.startsWith("/can-bo/ho-so/")) {
    return ["case-officer", "one-stop", "leader"].includes(role);
  }

  if (cleanPath === "/can-bo/bao-cao") {
    return ["case-officer", "leader"].includes(role);
  }

  if (cleanPath === "/can-bo/danh-muc-thu-tuc") {
    return ["case-officer", "one-stop", "leader"].includes(role);
  }

  if (cleanPath.startsWith("/can-bo/quan-tri")) {
    return false;
  }

  if (cleanPath === "/can-bo/tra-cuu") {
    return ["case-officer", "one-stop", "leader", "checkin"].includes(role);
  }

  return false;
}
