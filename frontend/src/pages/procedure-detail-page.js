import { el } from '../components/dom.js';
import { api, getAuthToken } from '../api/client.js';
import { openAuthModal } from '../components/auth-modal.js';

export function renderProcedureDetailPage({ params, navigate }) {
  const procedureId = params?.id || '1';
  const container = el('div', { class: 'container-portal', style: 'padding-top: 1.5rem; padding-bottom: 3.5rem;' });

  const contentArea = el('div', { style: 'margin-top: 1.5rem;' }, [
    el('div', { style: 'text-align: center; padding: 3rem; color: #64748b;' }, 'Đang tải thông tin chi tiết thủ tục...'),
  ]);

  function renderDetail(p) {
    const breadcrumb = el('div', {
      style: 'font-size: 12px; color: #64748b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;',
    }, [
      el('a', { href: '/', style: 'color: #004482; text-decoration: none;', onClick: (e) => { e.preventDefault(); navigate('/'); } }, 'Trang chủ'),
      el('span', {}, '/'),
      el('a', { href: '/thu-tuc', style: 'color: #004482; text-decoration: none;', onClick: (e) => { e.preventDefault(); navigate('/thu-tuc'); } }, 'Thủ tục hành chính'),
      el('span', {}, '/'),
      el('span', { style: 'font-weight: 600; color: #0f172a;' }, `TTHC-${p.id}`),
    ]);

    const headerCard = el('div', {
      class: 'card',
      style: 'padding: 1.75rem; background: #ffffff; border: 1px solid #d0d7de; margin-bottom: 1.5rem;',
    }, [
      el('div', { style: 'display: flex; gap: 0.5rem; margin-bottom: 0.5rem; align-items: center; flex-wrap: wrap;' }, [
        el('span', { style: 'font-size: 11px; font-weight: 600; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.15rem 0.5rem; border-radius: 3px;' }, p.field_name || 'Hành chính'),
      ]),
      el('h1', { style: 'font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: #004482; margin: 0.5rem 0 1.25rem; line-height: 1.4;' }, p.name),
      el('div', { style: 'display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;' }, [
        el('button', {
          type: 'button',
          class: 'btn btn-primary',
          style: 'background: #004482; font-weight: 700;',
          onClick: () => {
            if (!getAuthToken()) {
              openAuthModal('login', () => navigate(`/nop-ho-so?id=${p.id}`));
            } else {
              navigate(`/nop-ho-so?id=${p.id}`);
            }
          },
        }, 'Nộp hồ sơ trực tuyến'),
        el('button', {
          type: 'button',
          class: 'btn btn-secondary',
          style: 'border: 1px solid #cbd5e1; color: #334155;',
          onClick: () => window.print(),
        }, 'In hướng dẫn'),
      ]),
    ]);

    const generalInfoContent = el('div', { style: 'display: flex; flex-direction: column; gap: 0.75rem; font-size: 13.5px; line-height: 1.6;' }, [
      el('div', { style: 'padding-bottom: 0.6rem; border-bottom: 1px solid #f1f5f9;' }, [
        el('span', { style: 'font-weight: 700; color: #0f172a; margin-right: 0.5rem;' }, 'Cơ quan thực hiện:'),
        el('span', { style: 'color: #334155;' }, p.agency || 'Ủy ban nhân dân cấp xã'),
      ]),
      el('div', { style: 'padding-bottom: 0.6rem; border-bottom: 1px solid #f1f5f9;' }, [
        el('span', { style: 'font-weight: 700; color: #0f172a; margin-right: 0.5rem;' }, 'Đối tượng áp dụng:'),
        el('span', { style: 'color: #334155;' }, p.target || 'Công dân Việt Nam, Doanh nghiệp, Tổ chức'),
      ]),
      el('div', { style: 'padding-bottom: 0.6rem; border-bottom: 1px solid #f1f5f9;' }, [
        el('span', { style: 'font-weight: 700; color: #0f172a; margin-right: 0.5rem;' }, 'Thời hạn giải quyết:'),
        el('span', { style: 'color: #334155;' }, (p.methods?.[0]?.resolution_time) || 'Trong ngày làm việc hoặc theo quy định'),
      ]),
      el('div', { style: 'padding-top: 0.1rem;' }, [
        el('span', { style: 'font-weight: 700; color: #0f172a; margin-right: 0.5rem;' }, 'Kết quả thực hiện:'),
        el('span', { style: 'color: #004482; font-weight: 700;' }, p.result || 'Giấy chứng nhận / Bản sao kết quả theo quy định'),
      ]),
    ]);

    const methodsList = p.methods || [];
    const methodsContent = methodsList.length > 0 ? el('div', { style: 'display: flex; flex-direction: column; gap: 0.75rem;' },
      methodsList.map(m => el('div', {
        style: 'padding: 1rem; border: 1px solid #e2e8f0; border-radius: 4px; background: #f8fafc;',
      }, [
        el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;' }, [
          el('span', { style: 'font-weight: 700; font-size: 13.5px; color: #004482;' }, `Hình thức tiếp nhận: ${m.channel}`),
          m.resolution_time ? el('span', { style: 'font-size: 12px; color: #334155; font-weight: 600;' }, `Thời hạn: ${m.resolution_time}`) : null,
        ]),
        m.description ? el('p', { style: 'font-size: 13px; color: #1e293b; line-height: 1.5; margin: 0 0 0.5rem;' }, m.description) : null,
        m.fee_description ? el('div', { style: 'font-size: 12px; color: #475569; background: #ffffff; padding: 0.5rem; border-radius: 4px; border: 1px solid #e2e8f0;' }, [
          el('strong', { style: 'color: #0f172a;' }, 'Phí, lệ phí: '), m.fee_description,
        ]) : null,
      ]))
    ) : el('p', { style: 'font-size: 13px; color: #64748b; margin: 0;' }, 'Nộp hồ sơ trực tuyến qua Cổng Dịch vụ công hoặc trực tiếp tại Bộ phận Một cửa.');

    const componentsList = p.components || [];
    const dossierContent = componentsList.length > 0 ? el('div', { style: 'display: flex; flex-direction: column; gap: 1rem;' },
      componentsList.map(grp => el('div', {}, [
        grp.name ? el('h5', { style: 'font-size: 13px; font-weight: 700; color: #0f172a; margin: 0 0 0.5rem;' }, grp.name) : null,
        el('div', { style: 'overflow-x: auto;' }, [
          el('table', { style: 'width: 100%; border-collapse: collapse; font-size: 12.5px; background: #ffffff; border: 1px solid #d0d7de;' }, [
            el('thead', {}, el('tr', { style: 'background: #f8fafc; border-bottom: 1px solid #d0d7de; text-align: left;' }, [
              el('th', { style: 'padding: 0.6rem 0.75rem; width: 45px; color: #334155;' }, 'STT'),
              el('th', { style: 'padding: 0.6rem 0.75rem; color: #334155;' }, 'Tên giấy tờ, tài liệu'),
              el('th', { style: 'padding: 0.6rem 0.75rem; width: 120px; color: #334155;' }, 'Yêu cầu'),
              el('th', { style: 'padding: 0.6rem 0.75rem; width: 100px; text-align: center; color: #334155;' }, 'Bản chính'),
              el('th', { style: 'padding: 0.6rem 0.75rem; width: 100px; text-align: center; color: #334155;' }, 'Bản sao'),
            ])),
            el('tbody', {}, (grp.documents || []).map((doc, idx) => el('tr', {
              style: 'border-bottom: 1px solid #e2e8f0;',
            }, [
              el('td', { style: 'padding: 0.6rem 0.75rem; text-align: center; color: #64748b;' }, String(idx + 1)),
              el('td', { style: 'padding: 0.6rem 0.75rem;' }, [
                el('div', { style: 'font-weight: 600; color: #0f172a;' }, doc.name),
                doc.type ? el('span', { style: 'font-size: 11px; color: #64748b;' }, `Loại giấy tờ: ${doc.type}`) : null,
              ]),
              el('td', { style: 'padding: 0.6rem 0.75rem;' }, [
                el('span', {
                  style: `font-size: 11px; font-weight: 600; ${doc.required === 'Bắt buộc' ? 'color: #b91c1c;' : 'color: #64748b;'}`,
                }, doc.required || 'Theo yêu cầu'),
              ]),
              el('td', { style: 'padding: 0.6rem 0.75rem; text-align: center; color: #334155;' }, String(doc.original_copies ?? 0)),
              el('td', { style: 'padding: 0.6rem 0.75rem; text-align: center; color: #334155;' }, String(doc.duplicate_copies ?? 0)),
            ]))),
          ]),
        ]),
      ]))
    ) : el('p', { style: 'font-size: 13px; color: #64748b; margin: 0;' }, 'Thành phần hồ sơ theo mẫu quy định của cơ quan nhà nước có thẩm quyền.');

    const feesList = p.fees || [];
    const feesContent = feesList.length > 0 ? el('div', { style: 'overflow-x: auto;' }, [
      el('table', { style: 'width: 100%; border-collapse: collapse; font-size: 12.5px; background: #ffffff; border: 1px solid #d0d7de;' }, [
        el('thead', {}, el('tr', { style: 'background: #f8fafc; border-bottom: 1px solid #d0d7de; text-align: left;' }, [
          el('th', { style: 'padding: 0.6rem 0.75rem; width: 45px; color: #334155;' }, 'STT'),
          el('th', { style: 'padding: 0.6rem 0.75rem; color: #334155;' }, 'Tên loại phí / lệ phí'),
          el('th', { style: 'padding: 0.6rem 0.75rem; width: 140px; color: #334155;' }, 'Mức thu'),
          el('th', { style: 'padding: 0.6rem 0.75rem; color: #334155;' }, 'Ghi chú / Mô tả'),
        ])),
        el('tbody', {}, feesList.map((f, idx) => el('tr', { style: 'border-bottom: 1px solid #e2e8f0;' }, [
          el('td', { style: 'padding: 0.6rem 0.75rem; text-align: center; color: #64748b;' }, String(idx + 1)),
          el('td', { style: 'padding: 0.6rem 0.75rem; font-weight: 600; color: #0f172a;' }, f.type || 'Lệ phí'),
          el('td', { style: 'padding: 0.6rem 0.75rem; color: #0f172a; font-weight: 700;' }, `${Number(f.amount || 0).toLocaleString('vi-VN')} VNĐ`),
          el('td', { style: 'padding: 0.6rem 0.75rem; color: #475569;' }, f.description || '-'),
        ]))),
      ]),
    ]) : el('p', { style: 'font-size: 13px; color: #334155; font-weight: 600; margin: 0;' }, 'Không thu phí, lệ phí đối với thủ tục này.');

    const renderParagraphs = (text) => {
      if (!text) return el('p', { style: 'color: #64748b; font-size: 13px;' }, 'Chưa có thông tin.');
      const paras = text.split('\n').map(l => l.trim()).filter(Boolean);
      return el('div', { style: 'display: flex; flex-direction: column; gap: 0.6rem; font-size: 13px; line-height: 1.6; color: #1e293b;' },
        paras.map(item => el('p', { style: 'margin: 0;' }, item))
      );
    };

    const sectionElements = [];
    const makeSection = (title, contentNode, defaultOpen = true) => {
      let isOpen = defaultOpen;
      const body = el('div', { style: `display: ${isOpen ? 'block' : 'none'}; margin-top: 1rem; border-top: 1px solid #f1f5f9; padding-top: 1rem;` }, contentNode);
      const toggleBtn = el('button', {
        type: 'button',
        class: 'btn btn-ghost btn-sm',
        style: 'font-size: 12px; font-weight: 600; color: #004482; padding: 0.2rem 0.6rem;',
      }, isOpen ? 'Thu gọn ▲' : 'Mở rộng ▼');

      const toggle = () => {
        isOpen = !isOpen;
        body.style.display = isOpen ? 'block' : 'none';
        toggleBtn.textContent = isOpen ? 'Thu gọn ▲' : 'Mở rộng ▼';
      };

      const card = el('div', {
        class: 'card',
        style: 'padding: 1.25rem 1.5rem; background: #ffffff; border: 1px solid #d0d7de;',
      }, [
        el('div', {
          style: 'display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none;',
          onClick: toggle,
        }, [
          el('h3', {
            style: 'font-family: var(--font-heading); font-size: 15px; font-weight: 800; color: #004482; margin: 0;',
          }, title),
          toggleBtn,
        ]),
        body,
      ]);

      sectionElements.push({ setOpen: (open) => {
        isOpen = open;
        body.style.display = isOpen ? 'block' : 'none';
        toggleBtn.textContent = isOpen ? 'Thu gọn ▲' : 'Mở rộng ▼';
      }});
      return card;
    };

    const s1 = makeSection('I. Thông tin chung & Thẩm quyền giải quyết', generalInfoContent, true);
    const s2 = makeSection('II. Cách thức thực hiện', methodsContent, true);
    const s3 = makeSection('III. Trình tự thực hiện', renderParagraphs(p.instructions), true);
    const s4 = makeSection('IV. Thành phần hồ sơ', dossierContent, true);
    const s5 = makeSection('V. Phí, lệ phí', feesContent, true);
    const s6 = makeSection('VI. Yêu cầu, điều kiện thực hiện', renderParagraphs(p.requirements), false);
    const s7 = makeSection('VII. Căn cứ pháp lý', renderParagraphs(p.legal_basis), false);

    let allExpanded = false;
    const globalToggleBtn = el('button', {
      type: 'button',
      class: 'btn btn-ghost btn-sm',
      style: 'font-size: 12px; font-weight: 700; color: #004482; border: 1px solid #d0d7de; background: #ffffff;',
      onClick: () => {
        allExpanded = !allExpanded;
        sectionElements.forEach(s => s.setOpen(allExpanded));
        globalToggleBtn.textContent = allExpanded ? 'Thu gọn tất cả ▲' : 'Mở rộng tất cả ▼';
      },
    }, 'Mở rộng tất cả ▼');

    const toolbar = el('div', {
      style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0.5rem 0;',
    }, [
      el('span', { style: 'font-size: 12.5px; font-weight: 700; color: #0f172a;' }, 'QUY TRÌNH & THỦ TỤC CHI TIẾT'),
      globalToggleBtn,
    ]);

    const sectionsList = el('div', { style: 'display: flex; flex-direction: column; gap: 0.85rem;' }, [s1, s2, s3, s4, s5, s6, s7]);
    contentArea.replaceChildren(breadcrumb, headerCard, toolbar, sectionsList);
  }

  api.get(`/public/procedures/${procedureId}`).then(res => {
    if (res?.data) {
      renderDetail(res.data);
    } else {
      contentArea.replaceChildren(el('div', {
        class: 'card',
        style: 'padding: 2rem; text-align: center; color: #b91c1c; border: 1px solid #d0d7de; background: #ffffff;',
      }, 'Không tìm thấy thủ tục hành chính được yêu cầu.'));
    }
  }).catch(err => {
    contentArea.replaceChildren(el('div', {
      class: 'card',
      style: 'padding: 2rem; text-align: center; color: #b91c1c; border: 1px solid #d0d7de; background: #ffffff;',
    }, `Lỗi khi tải thông tin thủ tục: ${err.message || 'Thủ tục không tồn tại hoặc hệ thống gián đoạn'}`));
  });

  container.append(contentArea);
  return container;
}
