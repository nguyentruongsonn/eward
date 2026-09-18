/**
 * Report date normalization and validation utilities
 */

export function formatDateYMD(date) {
  const d = new Date(date);
  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

export function getDefaultReportRange(now = new Date()) {
  const toDate = new Date(now);
  const fromDate = new Date(now);
  fromDate.setDate(fromDate.getDate() - 29);

  return {
    from: formatDateYMD(fromDate),
    to: formatDateYMD(toDate),
  };
}

export function validateReportRange(from, to) {
  const ymdRegex = /^\d{4}-\d{2}-\d{2}$/;
  if (!from || !to || !ymdRegex.test(from) || !ymdRegex.test(to)) {
    return {
      valid: false,
      error: 'Vui lòng nhập định dạng ngày YYYY-MM-DD hợp lệ.',
    };
  }

  if (from > to) {
    return {
      valid: false,
      error: 'Khoảng ngày báo cáo không hợp lệ (Ngày bắt đầu phải trước hoặc bằng ngày kết thúc).',
    };
  }

  return {
    valid: true,
    from,
    to,
  };
}
