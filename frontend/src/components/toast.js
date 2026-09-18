import { el } from './dom.js';

let toastContainer = null;

function ensureContainer() {
  if (!toastContainer || !document.body.contains(toastContainer)) {
    toastContainer = el('div', {
      id: 'app-toast-container',
      style: 'position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 99999; display: flex; flex-direction: column; gap: 8px; align-items: center; pointer-events: none;',
    });
    document.body.appendChild(toastContainer);
  }
  return toastContainer;
}

export function showToast(message, type = 'info', duration = 3200) {
  const container = ensureContainer();

  const styles = {
    success: { accent: '#16a34a', iconBg: '#dcfce7', iconColor: '#15803d', icon: '✓' },
    error: { accent: '#dc2626', iconBg: '#fee2e2', iconColor: '#b91c1c', icon: '✕' },
    warning: { accent: '#d97706', iconBg: '#fef3c7', iconColor: '#b45309', icon: '!' },
    info: { accent: '#004482', iconBg: '#e0f2fe', iconColor: '#0369a1', icon: 'ℹ' },
  };
  const cfg = styles[type] || styles.info;

  const closeBtn = el('button', {
    type: 'button',
    style: 'border: none; background: none; color: #94a3b8; font-size: 13px; cursor: pointer; padding: 0 0 0 10px; line-height: 1; display: flex; align-items: center; transition: color 0.15s ease;',
    onClick: () => removeToast(),
  }, '✕');
  closeBtn.addEventListener('mouseenter', () => { closeBtn.style.color = '#334155'; });
  closeBtn.addEventListener('mouseleave', () => { closeBtn.style.color = '#94a3b8'; });

  const iconBadge = el('div', {
    style: `width: 20px; height: 20px; border-radius: 50%; background: ${cfg.iconBg}; color: ${cfg.iconColor}; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; flex-shrink: 0;`,
  }, cfg.icon);

  const toast = el('div', {
    class: 'top-toast',
    style: `pointer-events: auto; background: #ffffff; color: #0f172a; border: 1px solid #e2e8f0; border-left: 4px solid ${cfg.accent}; border-radius: 5px; padding: 0.65rem 1rem; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06); display: flex; align-items: center; gap: 0.65rem; font-size: 13px; min-width: 280px; max-width: 520px; opacity: 0; transform: translateY(-8px); transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);`,
  }, [
    iconBadge,
    el('span', { style: 'flex: 1; font-weight: 600; color: #1e293b; line-height: 1.4;' }, String(message)),
    closeBtn,
  ]);

  container.appendChild(toast);

  requestAnimationFrame(() => {
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
  });

  let timer = null;
  function removeToast() {
    if (timer) clearTimeout(timer);
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-8px)';
    setTimeout(() => { toast.remove(); }, 220);
  }

  if (duration > 0) {
    timer = setTimeout(removeToast, duration);
  }
}

showToast.success = (msg, d) => showToast(msg, 'success', d);
showToast.error = (msg, d) => showToast(msg, 'error', d);
showToast.warning = (msg, d) => showToast(msg, 'warning', d);
showToast.info = (msg, d) => showToast(msg, 'info', d);
