import { el } from '../components/dom.js';
import { openAuthModal } from '../components/auth-modal.js';

export function renderHomeBentoAndKpi({ navigate }) {
  const quickCards = [
    {
      title: 'Tra cứu thủ tục',
      action: () => navigate('/thu-tuc'),
    },
    {
      title: 'Nộp hồ sơ trực tuyến',
      action: () => openAuthModal('login'),
    },
    {
      title: 'Tra cứu tiến độ hồ sơ',
      action: () => navigate('/tra-cuu'),
    },
    {
      title: 'Đánh giá & Phản ánh',
      action: () => navigate('/phan-anh'),
    },
  ];

  const quickNavGrid = el('div', {
    style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-top: 1.5rem;',
  }, quickCards.map(c => el('div', {
    class: 'card',
    style: 'padding: 1.25rem 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; text-align: center; min-height: 68px; background: #004482; border: 1px solid #003666; border-radius: 6px; box-shadow: 0 2px 4px rgba(0, 68, 130, 0.15); transition: background 0.15s ease;',
    onClick: c.action,
  }, [
    el('h3', { style: 'font-family: var(--font-heading); font-size: 15px; font-weight: 700; color: #ffffff; margin: 0; line-height: 1.35;' }, c.title),
  ])));

  return el('section', { class: 'container-portal' }, quickNavGrid);
}
