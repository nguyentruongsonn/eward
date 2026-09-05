import { el } from './dom.js';
import { renderProfileModalInfoTab } from './profile-modal-info-tab.js';
import { renderProfileModalDossierTab } from './profile-modal-dossier-tab.js';
import { renderProfileModalFeeTab } from './profile-modal-fee-tab.js';

export function openApplicationDetailModal(app) {
  const overlay = el('div', {
    style: 'position: fixed; inset: 0; background: rgba(15,23,42,0.65); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 0.75rem;',
  });

  const modal = el('div', {
    class: 'card',
    style: 'background: #f8fafc; width: 98vw; max-width: 1400px; height: 95vh; max-height: 95vh; border-radius: 6px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); display: flex; flex-direction: column; overflow: hidden; border: 1px solid #cbd5e1;',
  });

  const appId = app.id || app.maHSXL || '';
  const procName = app.procedure_name || app.tenTTHC || 'Thủ tục hành chính';

  const header = el('div', {
    style: 'padding: 1rem 1.5rem; background: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;',
  }, [
    el('div', {}, [
      el('div', { style: 'font-size: 11px; font-weight: 800; color: #004b87; text-transform: uppercase; letter-spacing: 0.05em;' }, 'CHI TIẾT HỒ SƠ'),
      el('h2', { style: 'font-family: var(--font-heading); font-size: 17px; font-weight: 800; color: #004b87; margin: 0.2rem 0 0;' }, `${appId} — ${procName}`),
    ]),
    el('button', {
      type: 'button',
      class: 'btn btn-secondary btn-sm',
      style: 'border: 1px solid #cbd5e1; font-weight: 700; color: #475569; padding: 0.35rem 0.85rem; font-size: 12px;',
      onClick: closeModal,
    }, 'Đóng ✕'),
  ]);

  let activeTab = 'info';
  const tabNav = el('div', { style: 'display: flex; border-bottom: 1px solid #e2e8f0; background: #ffffff; padding: 0 1.5rem;' });
  const tabBody = el('div', { style: 'padding: 1.5rem; overflow-y: auto; flex: 1;' });

  const tabs = [
    { id: 'info', label: 'Thông tin chung' },
    { id: 'dossier', label: 'Thành phần hồ sơ' },
    { id: 'fee', label: 'Lệ phí & phí' },
  ];

  function renderTabs() {
    tabNav.replaceChildren(...tabs.map((t) => {
      const isActive = activeTab === t.id;
      return el('button', {
        type: 'button',
        style: `padding: 0.85rem 1.25rem; border: none; background: transparent; font-size: 13.5px; font-weight: 700; cursor: pointer; color: ${isActive ? '#004b87' : '#64748b'}; border-bottom: 2px solid ${isActive ? '#004b87' : 'transparent'}; margin-bottom: -1px; transition: color 0.15s ease;`,
        onClick: () => { activeTab = t.id; renderTabs(); renderContent(); },
      }, t.label);
    }));
  }

  function renderContent() {
    if (activeTab === 'info') {
      tabBody.replaceChildren(renderProfileModalInfoTab(app));
    } else if (activeTab === 'dossier') {
      tabBody.replaceChildren(renderProfileModalDossierTab(app));
    } else {
      tabBody.replaceChildren(renderProfileModalFeeTab(app));
    }
  }

  function closeModal() {
    overlay.remove();
    document.removeEventListener('keydown', onKeyDown);
  }

  function onKeyDown(e) {
    if (e.key === 'Escape') closeModal();
  }

  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) closeModal();
  });
  document.addEventListener('keydown', onKeyDown);

  renderTabs();
  renderContent();

  modal.append(header, tabNav, tabBody);
  overlay.append(modal);
  document.body.append(overlay);
}
