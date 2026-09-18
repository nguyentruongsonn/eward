import { el } from './dom.js';

function buildingIcon() {
  const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  s.setAttribute('width', '15'); s.setAttribute('height', '15');
  s.setAttribute('viewBox', '0 0 24 24'); s.setAttribute('fill', 'none');
  s.setAttribute('stroke', '#475569'); s.setAttribute('stroke-width', '2');
  s.setAttribute('stroke-linecap', 'round'); s.setAttribute('stroke-linejoin', 'round');
  s.innerHTML = '<path d="M3 21h18"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/><path d="M9 9h1"/><path d="M14 9h1"/><path d="M9 13h1"/><path d="M14 13h1"/><path d="M9 17h1"/><path d="M14 17h1"/>';
  return s;
}

function idCardIcon() {
  const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  s.setAttribute('width', '15'); s.setAttribute('height', '15');
  s.setAttribute('viewBox', '0 0 24 24'); s.setAttribute('fill', 'none');
  s.setAttribute('stroke', '#475569'); s.setAttribute('stroke-width', '2');
  s.setAttribute('stroke-linecap', 'round'); s.setAttribute('stroke-linejoin', 'round');
  s.innerHTML = '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="10" r="3"/><path d="M7 21v-1a5 5 0 0 1 10 0v1"/>';
  return s;
}

function clockIcon() {
  const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  s.setAttribute('width', '15'); s.setAttribute('height', '15');
  s.setAttribute('viewBox', '0 0 24 24'); s.setAttribute('fill', 'none');
  s.setAttribute('stroke', '#475569'); s.setAttribute('stroke-width', '2');
  s.setAttribute('stroke-linecap', 'round'); s.setAttribute('stroke-linejoin', 'round');
  s.innerHTML = '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 15 15"/>';
  return s;
}

function calendarIcon() {
  const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  s.setAttribute('width', '15'); s.setAttribute('height', '15');
  s.setAttribute('viewBox', '0 0 24 24'); s.setAttribute('fill', 'none');
  s.setAttribute('stroke', '#475569'); s.setAttribute('stroke-width', '2');
  s.setAttribute('stroke-linecap', 'round'); s.setAttribute('stroke-linejoin', 'round');
  s.innerHTML = '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="m9 16 2 2 4-4"/>';
  return s;
}

function formatDt(d) {
  if (!d) return '-';
  const dt = new Date(d);
  if (isNaN(dt.getTime())) return String(d);
  const pad = (n) => String(n).padStart(2, '0');
  return `${pad(dt.getDate())}/${pad(dt.getMonth() + 1)}/${dt.getFullYear()} ${pad(dt.getHours())}:${pad(dt.getMinutes())}:${pad(dt.getSeconds())}`;
}

function shiftMinutes(d, mins) {
  const dt = new Date(d);
  return new Date(dt.getTime() + mins * 60 * 1000);
}

export function renderWorkflowSteps(app) {
  const sid = Number(app.status?.id ?? 1);
  const notes = String(app.notes || app.ghiChu || '');
  const isRework = sid === 12 || notes.toLowerCase().includes('sửa lại') || notes.toLowerCase().includes('xem xét lại') || notes.toLowerCase().includes('xử lý lại');

  let activeStep = 1;
  let activeStepName = 'Cán bộ một cửa tiếp nhận';
  if (sid === 2 || sid === 5 || sid === 6) {
    activeStep = 2;
    activeStepName = sid === 5 ? 'Yêu cầu bổ sung' : (sid === 6 ? 'Tiếp tục xử lý sau bổ sung' : 'Chuyên viên thụ lý');
  } else if (sid === 12) {
    activeStep = 2;
    activeStepName = 'Chuyên viên xử lý lại';
  } else if (sid === 4) {
    activeStep = 3;
    activeStepName = 'Lãnh đạo phê duyệt';
  } else if (sid === 9 || sid === 10) {
    activeStep = 4;
    activeStepName = 'Cán bộ một cửa trả kết quả';
  }

  const t0 = app.ngayTiepNhan ? new Date(app.ngayTiepNhan) : (app.created_at ? new Date(app.created_at) : new Date());
  const dueDate = app.ngayHenTra ? new Date(app.ngayHenTra) : shiftMinutes(t0, 3 * 24 * 60);

  const receiver = app.receiver_name || 'Cán bộ một cửa';
  const approver = app.approver_name || 'Lãnh đạo UBND';
  const specialist = 'Chuyên viên xử lý hồ sơ';

  const t1Rec = t0;
  const t1Chuyen = activeStep > 1 ? shiftMinutes(t0, 2) : null;

  const t2Rec = t1Chuyen;
  const t2Chuyen = activeStep >= 3 ? (app.ngayDuyet ? shiftMinutes(new Date(app.ngayDuyet), -120) : shiftMinutes(t2Rec, 24 * 60)) : null;

  const t3Rec = t2Chuyen;
  const t3Chuyen = activeStep >= 4 ? (app.ngayDuyet ? new Date(app.ngayDuyet) : shiftMinutes(t3Rec, 4 * 60)) : null;

  const t4Rec = t3Chuyen;
  const t4Chuyen = (sid === 10 && app.ngayTra) ? new Date(app.ngayTra) : null;

  const allDefinitions = [
    {
      num: 1,
      name: 'Tiếp nhận hồ sơ',
      department: 'Bộ phận tiếp nhận và trả kết quả - UBND Phường',
      duration: '8 giờ làm việc',
      officer: receiver,
      received: formatDt(t1Rec),
      transferred: t1Chuyen ? formatDt(t1Chuyen) : 'Chưa chuyển (Đang tiếp nhận)',
      due: formatDt(dueDate),
      isCurrent: activeStep === 1,
      statusBadge: 'Đang thực hiện',
    },
    {
      num: 2,
      name: sid === 12 ? 'Thụ lý hồ sơ (Xử lý lại)' : 'Thụ lý hồ sơ',
      department: `Bộ phận chuyên môn thụ lý - ${app.procedure_field || 'UBND Phường'}`,
      duration: '16 giờ làm việc',
      officer: specialist,
      received: t2Rec ? formatDt(t2Rec) : '-',
      transferred: t2Chuyen ? formatDt(t2Chuyen) : (sid === 12 ? 'Đang xử lý lại theo ý kiến lãnh đạo' : (sid === 5 ? 'Chờ công dân bổ sung giấy tờ' : 'Chưa chuyển (Đang thụ lý)')),
      due: formatDt(dueDate),
      isCurrent: activeStep === 2,
      isReworkStep: sid === 12,
      statusBadge: sid === 12 ? 'Yêu cầu xử lý lại' : (sid === 5 ? 'Chờ bổ sung' : 'Đang thực hiện'),
    },
    {
      num: 3,
      name: 'Phê duyệt hồ sơ',
      department: 'Lãnh đạo UBND Phường (Chủ tịch / Phó Chủ tịch)',
      duration: '8 giờ làm việc',
      officer: approver,
      received: t3Rec ? formatDt(t3Rec) : '-',
      transferred: t3Chuyen ? formatDt(t3Chuyen) : 'Chưa chuyển (Đang xem xét phê duyệt)',
      due: formatDt(dueDate),
      isCurrent: activeStep === 3,
      statusBadge: 'Đang phê duyệt',
    },
    {
      num: 4,
      name: 'Trả kết quả',
      department: 'Bộ phận tiếp nhận và trả kết quả - UBND Phường',
      duration: '4 giờ làm việc',
      officer: receiver,
      received: t4Rec ? formatDt(t4Rec) : '-',
      transferred: t4Chuyen ? formatDt(t4Chuyen) : (sid === 10 ? 'Đã hoàn thành' : 'Chờ trả kết quả'),
      due: formatDt(dueDate),
      isCurrent: activeStep === 4 && sid !== 10,
      statusBadge: sid === 10 ? 'Đã hoàn thành' : 'Chờ trả kết quả',
    },
  ];

  const stepsToRender = allDefinitions.filter(s => s.num <= activeStep);

  return el('div', { style: 'display: flex; flex-direction: column; gap: 0.85rem; text-align: left;' },
    stepsToRender.map(s => el('div', {
      class: 'step-card',
      style: `background: #ffffff; border: 1px solid ${s.isCurrent ? '#93c5fd' : '#e2e8f0'}; border-radius: 4px; overflow: hidden; box-shadow: ${s.isCurrent ? '0 0 0 1px #93c5fd' : '0 1px 3px rgba(0,0,0,0.05)'};`,
    }, [
      el('div', {
        style: `background: ${s.isCurrent ? '#eff6ff' : '#eef2f9'}; padding: 0.65rem 1rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: flex-start;`,
      }, [
        el('div', {}, [
          el('div', { style: 'font-weight: 700; font-size: 13px; color: #1e293b;' }, `Bước: ${s.name}`),
          el('div', { style: 'display: flex; align-items: center; gap: 0.4rem; font-size: 12px; color: #475569; margin-top: 0.25rem;' }, [
            buildingIcon(),
            el('span', {}, s.department),
          ]),
          el('div', { style: 'font-size: 11.5px; color: #64748b; margin-top: 0.2rem;' }, `(${s.duration})`),
        ]),
        s.isCurrent ? el('span', {
          style: `font-size: 11px; font-weight: 700; ${s.isReworkStep ? 'color: #b91c1c; background: #fee2e2;' : 'color: #1d4ed8; background: #dbeafe;'} padding: 0.2rem 0.55rem; border-radius: 3px;`,
        }, s.statusBadge || 'Đang thực hiện') : el('span', {
          style: 'font-size: 11px; font-weight: 700; color: #15803d; background: #dcfce7; padding: 0.2rem 0.55rem; border-radius: 3px;',
        }, 'Đã hoàn thành'),
      ]),
      el('div', { style: 'padding: 0.75rem 1rem; display: flex; flex-direction: column; gap: 0.35rem; font-size: 12.5px; color: #1e293b;' }, [
        el('div', { style: 'display: flex; align-items: center; gap: 0.5rem;' }, [
          idCardIcon(),
          el('span', { style: 'color: #475569;' }, 'Người xử lý: '),
          el('strong', { style: 'color: #0f172a;' }, s.officer),
        ]),
        el('div', { style: 'display: flex; align-items: center; gap: 0.5rem;' }, [
          clockIcon(),
          el('span', { style: 'color: #475569;' }, 'Ngày tiếp nhận: '),
          el('span', { style: 'color: #0f172a;' }, s.received),
        ]),
        el('div', { style: 'display: flex; align-items: center; gap: 0.5rem;' }, [
          clockIcon(),
          el('span', { style: 'color: #475569;' }, 'Ngày chuyển hồ sơ: '),
          el('span', { style: `color: ${s.isCurrent ? '#d97706' : '#0f172a'}; font-weight: ${s.isCurrent ? '600' : 'normal'};` }, s.transferred),
        ]),
        el('div', { style: 'display: flex; align-items: center; gap: 0.5rem;' }, [
          calendarIcon(),
          el('span', { style: 'color: #475569;' }, 'Hạn xử lý: '),
          el('span', { style: 'color: #0f172a;' }, s.due),
        ]),
      ]),
    ]))
  );
}
