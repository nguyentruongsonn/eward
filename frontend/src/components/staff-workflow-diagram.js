import { el } from './dom.js';
import { renderWorkflowSteps } from './staff-workflow-steps.js';

let activeWorkflowModal = null;

export function buildWorkflowSvg(app) {
  const sid = Number(app.status?.id ?? 1);
  const notes = String(app.notes || app.ghiChu || '');
  const isRework = sid === 12 || notes.toLowerCase().includes('sửa lại') || notes.toLowerCase().includes('xem xét lại') || notes.toLowerCase().includes('xử lý lại');

  let activeStep = 1;
  let statusText = 'Cán bộ một cửa tiếp nhận';
  if (sid === 2 || sid === 5 || sid === 6) {
    activeStep = 2;
    statusText = sid === 5 ? 'Yêu cầu bổ sung giấy tờ' : (sid === 6 ? 'Tiếp tục xử lý sau bổ sung' : 'Chuyên viên đang thụ lý');
  } else if (sid === 12) {
    activeStep = 2;
    statusText = 'Lãnh đạo yêu cầu xử lý lại';
  } else if (sid === 4) {
    activeStep = 3;
    statusText = 'Chờ lãnh đạo phê duyệt';
  } else if (sid === 9) {
    activeStep = 4;
    statusText = 'Đã phê duyệt (Chờ trả kết quả)';
  } else if (sid === 10) {
    activeStep = 4;
    statusText = 'Cán bộ một cửa trả kết quả';
  }

  const svgNS = 'http://www.w3.org/2000/svg';
  const svg = document.createElementNS(svgNS, 'svg');
  svg.setAttribute('viewBox', '0 0 790 155');
  svg.setAttribute('style', 'width: 100%; max-width: 790px; height: auto; display: block; margin: 0 auto; background: #ffffff;');

  const defs = document.createElementNS(svgNS, 'defs');
  defs.innerHTML = `
    <marker id="wf-arr" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse"><path d="M 0 1.5 L 7 5 L 0 8.5 z" fill="#000000" /></marker>
    <marker id="wf-arr-down" viewBox="0 0 10 10" refX="5" refY="6" markerWidth="6" markerHeight="6" orient="auto"><path d="M 1.5 0 L 5 7 L 8.5 0 z" fill="#000000" /></marker>
  `;
  svg.appendChild(defs);

  function line(x1, y1, x2, y2, marker = 'wf-arr') {
    const l = document.createElementNS(svgNS, 'line');
    l.setAttribute('x1', x1); l.setAttribute('y1', y1); l.setAttribute('x2', x2); l.setAttribute('y2', y2);
    l.setAttribute('stroke', '#000000'); l.setAttribute('stroke-width', '1.6');
    if (marker) l.setAttribute('marker-end', `url(#${marker})`);
    return l;
  }

  const spots = {
    1: { cx: 75, cy: 95, kx: 72, ky: 93 },
    2: isRework ? { cx: 297, cy: 45, kx: 294, ky: 41 } : { cx: 230, cy: 95, kx: 227, ky: 93 },
    3: { cx: 385, cy: 95, kx: 382, ky: 93 },
    4: { cx: 600, cy: 95, kx: 597, ky: 93 },
  };
  const curSpot = spots[activeStep] || spots[1];

  const glow = document.createElementNS(svgNS, 'circle');
  glow.setAttribute('cx', String(curSpot.cx)); glow.setAttribute('cy', String(curSpot.cy));
  glow.setAttribute('r', '22'); glow.setAttribute('fill', '#fef08a');
  glow.setAttribute('opacity', '0.85');
  svg.appendChild(glow);

  const cursor = document.createElementNS(svgNS, 'polygon');
  cursor.setAttribute('points', `${curSpot.kx},${curSpot.ky} ${curSpot.kx},${curSpot.ky + 15} ${curSpot.kx + 4},${curSpot.ky + 11} ${curSpot.kx + 7},${curSpot.ky + 17} ${curSpot.kx + 9},${curSpot.ky + 16} ${curSpot.kx + 6},${curSpot.ky + 10} ${curSpot.kx + 11},${curSpot.ky + 10}`);
  cursor.setAttribute('fill', '#ffffff'); cursor.setAttribute('stroke', '#000000');
  cursor.setAttribute('stroke-width', '1.3');
  svg.appendChild(cursor);

  const startCircle = document.createElementNS(svgNS, 'circle');
  startCircle.setAttribute('cx', '35'); startCircle.setAttribute('cy', '95'); startCircle.setAttribute('r', '12');
  startCircle.setAttribute('fill', '#ffffff'); startCircle.setAttribute('stroke', '#000000');
  startCircle.setAttribute('stroke-width', '1.8');
  svg.appendChild(startCircle);
  svg.appendChild(line(47, 95, 85, 95));

  const steps = [
    { num: 1, x: 85, y: 65, w: 115, h: 60, l1: 'Cán bộ một cửa', l2: 'tiếp nhận' },
    { num: 2, x: 240, y: 65, w: 115, h: 60, l1: 'Chuyên viên xử', l2: 'lý' },
    { num: 3, x: 395, y: 65, w: 115, h: 60, l1: 'Lãnh đạo', l2: 'phê duyệt' },
    { num: 4, x: 610, y: 65, w: 115, h: 60, l1: 'Cán bộ một cửa', l2: 'trả kết quả' },
  ];

  svg.appendChild(line(200, 95, 240, 95));
  svg.appendChild(line(355, 95, 395, 95));
  svg.appendChild(line(510, 95, 542, 95));

  const gw = document.createElementNS(svgNS, 'polygon');
  gw.setAttribute('points', '556,77 570,95 556,113 542,95'); gw.setAttribute('fill', '#ffffff');
  gw.setAttribute('stroke', '#000000'); gw.setAttribute('stroke-width', '1.8');
  svg.appendChild(gw);

  const gwX = document.createElementNS(svgNS, 'text');
  gwX.setAttribute('x', '556'); gwX.setAttribute('y', '101'); gwX.setAttribute('text-anchor', 'middle');
  gwX.setAttribute('font-size', '16'); gwX.setAttribute('font-weight', 'bold'); gwX.setAttribute('font-family', 'sans-serif');
  gwX.setAttribute('fill', '#000000'); gwX.textContent = 'X';
  svg.appendChild(gwX);

  const loopPath = document.createElementNS(svgNS, 'path');
  loopPath.setAttribute('d', 'M 556,77 L 556,22 L 297,22 L 297,63');
  loopPath.setAttribute('fill', 'none'); loopPath.setAttribute('stroke', '#000000');
  loopPath.setAttribute('stroke-width', '1.6'); loopPath.setAttribute('marker-end', 'url(#wf-arr-down)');
  svg.appendChild(loopPath);

  svg.appendChild(line(570, 95, 610, 95));
  svg.appendChild(line(725, 95, 755, 95));

  const endOuter = document.createElementNS(svgNS, 'circle');
  endOuter.setAttribute('cx', '767'); endOuter.setAttribute('cy', '95'); endOuter.setAttribute('r', '12');
  endOuter.setAttribute('fill', '#ffffff'); endOuter.setAttribute('stroke', '#000000'); endOuter.setAttribute('stroke-width', '3.5');
  svg.appendChild(endOuter);

  steps.forEach(s => {
    const rect = document.createElementNS(svgNS, 'rect');
    rect.setAttribute('x', String(s.x)); rect.setAttribute('y', String(s.y));
    rect.setAttribute('width', String(s.w)); rect.setAttribute('height', String(s.h));
    rect.setAttribute('rx', '8'); rect.setAttribute('fill', '#ffffff');
    rect.setAttribute('stroke', '#000000'); rect.setAttribute('stroke-width', '1.8');
    svg.appendChild(rect);

    const head = document.createElementNS(svgNS, 'circle');
    head.setAttribute('cx', String(s.x + 13)); head.setAttribute('cy', String(s.y + 13));
    head.setAttribute('r', '3'); head.setAttribute('fill', 'none');
    head.setAttribute('stroke', '#000000'); head.setAttribute('stroke-width', '1.2');
    svg.appendChild(head);

    const body = document.createElementNS(svgNS, 'path');
    body.setAttribute('d', `M ${s.x + 7} ${s.y + 22} A 6 6 0 0 1 ${s.x + 19} ${s.y + 22}`);
    body.setAttribute('fill', 'none'); body.setAttribute('stroke', '#000000');
    body.setAttribute('stroke-width', '1.2');
    svg.appendChild(body);

    const t1 = document.createElementNS(svgNS, 'text');
    t1.setAttribute('x', String(s.x + s.w / 2)); t1.setAttribute('y', String(s.y + 30));
    t1.setAttribute('text-anchor', 'middle'); t1.setAttribute('font-size', '11');
    t1.setAttribute('font-family', 'sans-serif'); t1.setAttribute('fill', '#000000');
    t1.textContent = s.l1; svg.appendChild(t1);

    const t2 = document.createElementNS(svgNS, 'text');
    t2.setAttribute('x', String(s.x + s.w / 2)); t2.setAttribute('y', String(s.y + 45));
    t2.setAttribute('text-anchor', 'middle'); t2.setAttribute('font-size', '11');
    t2.setAttribute('font-family', 'sans-serif'); t2.setAttribute('fill', '#000000');
    t2.textContent = s.l2; svg.appendChild(t2);
  });

  return { svg, activeStep, statusText, steps };
}

export function openStaffWorkflowModal(app) {
  if (activeWorkflowModal) {
    activeWorkflowModal.remove();
    activeWorkflowModal = null;
  }
  const { svg, activeStep, statusText, steps } = buildWorkflowSvg(app);

  const backdrop = el('div', {
    class: 'modal-backdrop',
    style: 'position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); z-index: 1050; display: flex; align-items: center; justify-content: center; padding: 1rem;',
  });

  const onKey = (e) => { if (e.key === 'Escape') close(); };
  const close = () => {
    window.removeEventListener('keydown', onKey);
    backdrop.remove();
    activeWorkflowModal = null;
  };
  window.addEventListener('keydown', onKey);
  backdrop.addEventListener('click', close);

  const leftCol = el('div', {
    style: 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;',
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;' }, [
      el('h4', { style: 'font-size: 13px; font-weight: 800; color: #004482; margin: 0; text-transform: uppercase;' }, 'Sơ đồ quy trình BPMN'),
      el('span', { style: 'font-size: 11.5px; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 0.2rem 0.6rem; border-radius: 3px;' }, `Bước hiện tại: ${activeStep}/4`),
    ]),
    el('div', { style: 'overflow-x: auto; padding: 0.75rem 0; text-align: center;' }, [svg]),
    el('div', { style: 'background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 0.75rem; font-size: 12px; display: flex; flex-direction: column; gap: 0.35rem;' }, [
      el('div', { style: 'font-weight: 700; color: #334155; margin-bottom: 0.2rem;' }, 'Danh mục các bước chuẩn:'),
      ...steps.map(s => {
        const isCur = s.num === activeStep;
        const isDone = s.num < activeStep;
        return el('div', { style: `display: flex; justify-content: space-between; padding: 0.25rem 0.5rem; border-radius: 3px; ${isCur ? 'background: #eff6ff; font-weight: 700; color: #1d4ed8;' : (isDone ? 'color: #166534;' : 'color: #64748b;')}` }, [
          el('span', {}, `${s.num}. ${s.l1} ${s.l2}`),
          el('span', { style: 'font-size: 11px;' }, isCur ? '● Đang thực hiện' : (isDone ? '✓ Đã hoàn thành' : '○ Chờ giải quyết')),
        ]);
      }),
    ]),
  ]);

  const rightCol = el('div', {
    style: 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem; max-height: 75vh; overflow-y: auto;',
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;' }, [
      el('h4', { style: 'font-size: 13px; font-weight: 800; color: #004482; margin: 0; text-transform: uppercase;' }, 'Tiến độ luân chuyển hồ sơ'),
      el('span', { style: 'font-size: 11.5px; color: #64748b;' }, `Thực tế: ${activeStep}/4 bước`),
    ]),
    renderWorkflowSteps(app),
  ]);

  const grid2Col = el('div', {
    style: 'display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr); gap: 1.25rem; align-items: start;',
  }, [leftCol, rightCol]);

  const card = el('div', {
    class: 'card',
    style: 'width: 96%; max-width: 1220px; background: #ffffff; border-radius: 8px; padding: 1.5rem; box-shadow: 0 16px 40px rgba(0,0,0,0.25); max-height: 92vh; overflow-y: auto;',
    onClick: (e) => e.stopPropagation(),
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.65rem;' }, [
      el('h3', { style: 'font-size: 16px; font-weight: 800; color: #004482; margin: 0; text-transform: uppercase;' }, 'Quy trình xử lý hồ sơ'),
      el('button', { type: 'button', style: 'border: none; background: none; font-size: 20px; color: #64748b; cursor: pointer; padding: 0.25rem;', onClick: close }, '✕'),
    ]),
    grid2Col,
    el('div', { style: 'display: flex; justify-content: flex-end; margin-top: 1.25rem; border-top: 1px solid #f1f5f9; padding-top: 0.75rem;' }, [
      el('button', { type: 'button', class: 'btn btn-secondary btn-sm', style: 'height: 34px; padding: 0 1.5rem; font-weight: 600;', onClick: close }, 'Đóng'),
    ]),
  ]);

  backdrop.append(card);
  document.body.append(backdrop);
  activeWorkflowModal = backdrop;
}
