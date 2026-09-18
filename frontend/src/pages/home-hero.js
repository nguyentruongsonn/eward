import { el } from '../components/dom.js';

export function renderHomeHero({ navigate }) {
  let activeTab = 'thu-tuc';

  const tabDefs = [
    { id: 'thu-tuc', label: 'Thủ tục hành chính' },
    { id: 'lien-thong', label: 'Dịch vụ công liên thông' },
    { id: 'van-ban', label: 'Tra cứu văn bản' },
  ];

  const searchInput = el('input', {
    type: 'search',
    class: 'hero-search-input',
    placeholder: 'Nhập tên thủ tục, từ khóa hoặc mã hồ sơ (ví dụ: Khai sinh, Cư trú, Đất đai)...',
    style: 'flex: 1; height: 46px; border: none; font-size: 13.5px; outline: none; padding: 0 0.75rem; color: #0f172a;',
    onKeydown: (e) => {
      if (e.key === 'Enter') handleSearch();
    },
  });

  const handleSearch = () => {
    const q = searchInput.value.trim();
    navigate(`/thu-tuc?q=${encodeURIComponent(q)}`);
  };

  const tabsContainer = el('div', {
    style: 'display: flex; border-bottom: 1px solid #e2e8f0; background: #f8fafc;',
  }, tabDefs.map(t => el('button', {
    type: 'button',
    style: `padding: 0.65rem 1.15rem; font-size: 13px; font-weight: 600; border-bottom: 2px solid ${activeTab === t.id ? '#004482' : 'transparent'}; color: ${activeTab === t.id ? '#004482' : '#64748b'}; background: ${activeTab === t.id ? '#ffffff' : 'transparent'}; transition: all 0.15s;`,
    onClick: () => {
      activeTab = t.id;
      Array.from(tabsContainer.children).forEach((btn, idx) => {
        const isCur = tabDefs[idx].id === activeTab;
        btn.style.borderBottomColor = isCur ? '#004482' : 'transparent';
        btn.style.color = isCur ? '#004482' : '#64748b';
        btn.style.background = isCur ? '#ffffff' : 'transparent';
      });
    },
  }, t.label)));

  const searchBox = el('div', {
    style: 'max-width: 840px; margin: 1.75rem auto 1rem; background: #ffffff; border-radius: 4px; border: 1px solid rgba(255,255,255,0.25); box-shadow: 0 4px 20px rgba(0, 35, 75, 0.15); overflow: hidden; text-align: left;',
  }, [
    tabsContainer,
    el('div', {
      style: 'display: flex; align-items: center; padding: 0.4rem 0.5rem; background: #ffffff; gap: 0.5rem;',
    }, [
      el('span', { class: 'material-symbols-outlined', style: 'color: #64748b; font-size: 20px; margin-left: 0.5rem;' }, 'search'),
      searchInput,
      el('button', {
        type: 'button',
        class: 'btn btn-primary',
        style: 'height: 40px; padding: 0 1.5rem; border-radius: 4px; font-size: 13.5px; font-weight: 700; background: #004482;',
        onClick: handleSearch,
      }, 'Tra cứu'),
    ]),
  ]);

  const keywords = [
    'Đăng ký khai sinh',
    'Cấp phép xây dựng',
    'Đăng ký kết hôn',
    'Chứng thực bản sao',
    'Trích lục hộ tịch',
  ];

  const quickElements = keywords.map(tag => el('button', {
    type: 'button',
    style: 'color: #004b87; font-size: 12px; font-weight: 600; background: #ffffff; border: 1px solid #cbd5e1; padding: 0.25rem 0.65rem; border-radius: 4px; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.06); transition: all 0.15s ease;',
    onClick: () => {
      searchInput.value = tag;
      handleSearch();
    },
    onMouseenter: (e) => {
      e.currentTarget.style.borderColor = '#004b87';
      e.currentTarget.style.background = '#f0f7ff';
    },
    onMouseleave: (e) => {
      e.currentTarget.style.borderColor = '#cbd5e1';
      e.currentTarget.style.background = '#ffffff';
    },
  }, tag));

  const quickRow = el('div', {
    style: 'font-size: 12.5px; color: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 0.45rem; margin-top: 0.85rem;',
  }, [
    el('span', { style: 'font-weight: 600; color: rgba(255, 255, 255, 0.85); margin-right: 0.25rem;' }, 'Từ khóa phổ biến:'),
    ...quickElements,
  ]);

  return el('section', {
    class: 'hero-section',
    style: 'background: linear-gradient(180deg, #0b4d8c 0%, #073866 100%); color: #ffffff; padding: 2.5rem 0 3rem; text-align: center;',
  }, [
    el('div', { class: 'container-portal' }, [
      searchBox,
      quickRow,
    ]),
  ]);
}
