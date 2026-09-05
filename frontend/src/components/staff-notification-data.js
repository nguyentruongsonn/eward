/**
 * Default sample notifications tailored for each staff role
 */
export function getDefaultNotifications(role) {
  if (role === 'case-officer' || role === 'staff') {
    return [
      {
        id: 'mock-1',
        title: 'Lãnh đạo yêu cầu xử lý lại hồ sơ',
        content: 'Chủ tịch UBND yêu cầu rà soát thành phần hồ sơ và đối chiếu bản chính HSXL_20260910_0153.',
        time: '15 phút trước',
        path: '/can-bo/ho-so',
        type: 'rework',
      },
      {
        id: 'mock-2',
        title: 'Cán bộ Một cửa chuyển xử lý',
        content: 'Bộ phận Một cửa đã tiếp nhận và chuyển tiếp hồ sơ HSXL_20260910_0154 cho bạn thụ lý.',
        time: '45 phút trước',
        path: '/can-bo/ho-so',
        type: 'forward',
      },
      {
        id: 'mock-3',
        title: 'Lãnh đạo đã phê duyệt hồ sơ',
        content: 'Hồ sơ Cấp bản sao Trích lục hộ tịch HSXL_20260910_0150 đã được ký duyệt.',
        time: '2 giờ trước',
        path: '/can-bo/ho-so',
        type: 'approve',
      },
    ];
  }

  if (role === 'leader') {
    return [
      {
        id: 'mock-l1',
        title: 'Hồ sơ mới trình phê duyệt',
        content: 'Cán bộ thụ lý vừa hoàn tất thẩm định và trình phê duyệt hồ sơ HSXL_20260910_0152.',
        time: '20 phút trước',
        path: '/can-bo/ho-so?status=4',
        type: 'forward',
      },
      {
        id: 'mock-l2',
        title: 'Hồ sơ yêu cầu xử lý lại đã cập nhật',
        content: 'Cán bộ thụ lý đã bổ sung giải trình theo ý kiến chỉ đạo đối với HSXL_20260910_0148.',
        time: '1 giờ trước',
        path: '/can-bo/ho-so?status=4',
        type: 'approve',
      },
    ];
  }

  return [
    {
      id: 'mock-o1',
      title: 'Hồ sơ nộp trực tuyến mới',
      content: 'Công dân vừa nộp trực tuyến hồ sơ Đăng ký khai sinh HSXL_20260910_0155.',
      time: '10 phút trước',
      path: '/can-bo/ho-so?status=1',
      type: 'forward',
    },
    {
      id: 'mock-o2',
      title: 'Hồ sơ sẵn sàng trả kết quả',
      content: 'Hồ sơ HSXL_20260910_0146 đã có chữ ký số và kết quả giải quyết hoàn tất.',
      time: '1 giờ trước',
      path: '/can-bo/ho-so?status=9',
      type: 'approve',
    },
  ];
}

export function createBellIcon() {
  const span = document.createElement('span');
  span.style.display = 'inline-flex';
  span.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#004b87" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>`;
  return span;
}

export function createNotificationTypeIcon(type) {
  const span = document.createElement('span');
  span.style.display = 'inline-flex';
  if (type === 'rework') {
    span.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;
  } else if (type === 'forward') {
    span.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>`;
  } else {
    span.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
  }
  return span;
}
