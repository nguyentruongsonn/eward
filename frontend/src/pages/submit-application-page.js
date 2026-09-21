import { el } from '../components/dom.js';
import { api, getAuthToken, getStoredUser, setStoredUser } from '../api/client.js';
import { createApplicationCheckout, pollPaymentIntent, renderPaymentQr } from '../api/payment-checkout.js';
import { openAuthModal } from '../components/auth-modal.js';
import { createDynamicForm } from '../components/dynamic-form.js';
import { createSubmissionDossier } from '../components/submission-dossier.js';
import { createStep3Fee, createStep4Review } from '../components/submission-review.js';

export function renderSubmitApplicationPage({ params, searchParams, navigate }) {
  const procedureId = params?.id || searchParams?.get('id') || '1';
  const container = el('div', { class: 'container-portal', style: 'padding: 2rem 1rem 4rem;' });

  const breadcrumb = el('div', { style: 'font-size: 12px; color: #64748b; margin-bottom: 1.5rem;' }, [
    el('a', { href: '/', style: 'color: #004482; text-decoration: none;', onClick: (e) => { e.preventDefault(); navigate('/'); } }, 'Trang chủ'),
    el('span', { style: 'margin: 0 0.5rem;' }, '›'),
    el('a', { href: '/thu-tuc', style: 'color: #004482; text-decoration: none;', onClick: (e) => { e.preventDefault(); navigate('/thu-tuc'); } }, 'Thủ tục hành chính'),
    el('span', { style: 'margin: 0 0.5rem;' }, '›'),
    el('span', { style: 'font-weight: 600; color: #0f172a;' }, 'Nộp hồ sơ trực tuyến'),
  ]);

  const mainArea = el('div', { style: 'margin-top: 1rem;' });
  container.append(breadcrumb, mainArea);

  async function checkAuthAndRender() {
    const token = getAuthToken();
    let user = getStoredUser();

    if (!token) {
      mainArea.replaceChildren(el('div', {
        class: 'card',
        style: 'padding: 2.5rem; max-width: 600px; margin: 0 auto; text-align: center; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;',
      }, [
        el('div', { style: 'display: flex; justify-content: center; margin-bottom: 1rem;' },
          el('span', { class: 'material-symbols-outlined', style: 'font-size: 40px; color: #004482;' }, 'lock')
        ),
        el('h2', { style: 'font-family: var(--font-heading); font-size: 18px; font-weight: 800; color: #004482; margin-bottom: 0.5rem;' }, 'YÊU CẦU XÁC THỰC DANH TÍNH'),
        el('p', { style: 'font-size: 13px; color: #475569; line-height: 1.6; margin-bottom: 1.5rem;' }, 'Quý công dân vui lòng đăng nhập trước khi nộp hồ sơ trực tuyến.'),
        el('div', { style: 'display: flex; justify-content: center; gap: 0.75rem;' }, [
          el('button', { type: 'button', class: 'btn btn-primary', style: 'background: #004482; font-weight: 700;', onClick: () => openAuthModal('login', () => checkAuthAndRender()) }, 'Đăng nhập ngay'),
        ]),
      ]));
      return;
    }

    if (!user) {
      try {
        const meRes = await api.get('/auth/me');
        if (meRes?.data) {
          user = meRes.data;
          setStoredUser(user);
        }
      } catch (_) {}
    }

    loadProcedureAndInitWizard(user || {});
  }

  async function loadProcedureAndInitWizard(user) {
    mainArea.replaceChildren(el('div', { style: 'text-align: center; padding: 3rem; color: #64748b;' }, 'Đang tải biểu mẫu thủ tục...'));
    try {
      const res = await api.get(`/public/procedures/${procedureId}`);
      initWizard(res.data || res, user);
    } catch (err) {
      mainArea.replaceChildren(el('div', { class: 'card', style: 'padding: 2rem; color: #b91c1c;' }, `Lỗi tải thủ tục: ${err.message}`));
    }
  }

  function initWizard(proc, user) {
    let currentStep = 1;
    let step1Data = {};
    let step2Files = [];
    let step3Data = {};

    const stepsInfo = [
      { num: 1, title: 'Điền thông tin hồ sơ' },
      { num: 2, title: 'Thành phần hồ sơ' },
      { num: 3, title: 'Lệ phí & Nhận kết quả' },
      { num: 4, title: 'Xác nhận & Nộp' },
    ];

    const stepperBar = el('div', {
      class: 'wizard-stepper-bar',
      style: 'display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-bottom: 1.5rem; background: #ffffff; padding: 0.75rem; border: 1px solid #d0d7de; border-radius: 4px;',
    });

    const errorAlert = el('div', {
      style: 'display: none; padding: 0.75rem 1rem; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; font-size: 13px; margin-bottom: 1rem;',
    });

    const dynamicForm = createDynamicForm(proc.form_config, user);
    const dossier = createSubmissionDossier(proc.components);
    const step3 = createStep3Fee(proc);

    const contentBox = el('div', { class: 'wizard-content-box' });

    function renderStepperHeader() {
      stepperBar.replaceChildren(...stepsInfo.map(s => {
        const isCurrent = s.num === currentStep;
        const isDone = s.num < currentStep;
        return el('div', {
          style: `display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.65rem; border-radius: 4px; font-size: 12px; font-weight: 700; ${isCurrent ? 'background: #e0f2fe; color: #004482; border: 1px solid #bae6fd;' : (isDone ? 'background: #f0fdf4; color: #166534;' : 'color: #64748b;')}`,
        }, [
          el('span', {
            style: `width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 11px; ${isCurrent ? 'background: #004482; color: #fff;' : (isDone ? 'background: #166534; color: #fff;' : 'background: #e2e8f0; color: #64748b;')}`,
          }, isDone ? '✓' : String(s.num)),
          el('span', { class: 'step-title-text' }, `BƯỚC ${s.num}: ${s.title}`),
        ]);
      }));
    }

    const prevBtn = el('button', { type: 'button', class: 'btn btn-secondary', style: 'padding: 0.6rem 1.25rem;', onClick: () => goToStep(currentStep - 1) }, '← Quay lại');
    const nextBtn = el('button', { type: 'button', class: 'btn btn-primary', style: 'background: #004482; font-weight: 700; padding: 0.6rem 1.5rem;' });

    const navBar = el('div', {
      style: 'display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #e2e8f0;',
    }, [prevBtn, nextBtn]);

    function goToStep(step) {
      errorAlert.style.display = 'none';
      if (step < 1 || step > 4) return;
      currentStep = step;
      renderStepperHeader();

      prevBtn.style.visibility = currentStep === 1 ? 'hidden' : 'visible';
      nextBtn.textContent = currentStep === 4 ? 'Xác nhận nộp hồ sơ chính thức ✓' : `Tiếp tục sang Bước ${currentStep + 1} →`;

      if (currentStep === 1) {
        contentBox.replaceChildren(dynamicForm.element);
      } else if (currentStep === 2) {
        contentBox.replaceChildren(dossier.element);
      } else if (currentStep === 3) {
        contentBox.replaceChildren(step3.element);
      } else if (currentStep === 4) {
        step3Data = step3.getData();
        const review = createStep4Review({ proc, step1Data, step2Files, step3Data });
        contentBox.replaceChildren(review.element);
        contentBox._reviewInstance = review;
      }
      window.scrollTo({ top: container.offsetTop, behavior: 'smooth' });
    }

    nextBtn.addEventListener('click', async () => {
      errorAlert.style.display = 'none';
      if (currentStep === 1) {
        const val = dynamicForm.validate();
        if (!val.valid) {
          errorAlert.textContent = val.message;
          errorAlert.style.display = 'block';
          window.scrollTo({ top: container.offsetTop, behavior: 'smooth' });
          return;
        }
        step1Data = dynamicForm.getValues();
        goToStep(2);
      } else if (currentStep === 2) {
        const val = dossier.validate();
        if (!val.valid) {
          errorAlert.textContent = val.message;
          errorAlert.style.display = 'block';
          window.scrollTo({ top: container.offsetTop, behavior: 'smooth' });
          return;
        }
        step2Files = dossier.getAttachedFiles();
        goToStep(3);
      } else if (currentStep === 3) {
        step3Data = step3.getData();
        goToStep(4);
      } else if (currentStep === 4) {
        const review = contentBox._reviewInstance;
        if (!review || !review.isConfirmed()) {
          errorAlert.textContent = 'Quý công dân vui lòng tích chọn ô "Cam đoan trách nhiệm pháp lý" trước khi gửi hồ sơ.';
          errorAlert.style.display = 'block';
          return;
        }

        nextBtn.disabled = true;
        nextBtn.textContent = 'Đang xử lý nộp hồ sơ...';

        const applicantName = step1Data.ho_ten || step1Data.full_name || user.full_name || user.hoTen || 'Công dân';
        const applicantPhone = step1Data.so_dien_thoai || step1Data.phone || user.phone || '';
        const applicantCccd = step1Data.so_giay_to || step1Data.so_cmnd_cccd || step1Data.citizen_id || user.citizen_id || '';

        const payload = {
          procedure_id: parseInt(proc.id),
          data: {
            ...step1Data,
            full_name: applicantName,
            citizen_id: applicantCccd,
            phone: applicantPhone,
            attached_files: step2Files,
            delivery_method: step3Data.delivery_method,
            payment_method: step3Data.payment_method,
          },
          delivery_method: step3Data.delivery_method,
          payment_method: step3Data.payment_method,
          fee_items: step3Data.fee_items || [],
        };

        try {
          const res = await api.post('/citizen/applications', payload);
          renderSuccessReceipt(res.data || res, applicantPhone, step3Data.payment_method);
        } catch (err) {
          errorAlert.textContent = err.message || 'Không thể gửi hồ sơ. Vui lòng kiểm tra lại thông tin.';
          errorAlert.style.display = 'block';
          nextBtn.disabled = false;
          nextBtn.textContent = 'Xác nhận nộp hồ sơ chính thức ✓';
        }
      }
    });

    const headerCard = el('div', { class: 'card', style: 'padding: 1.25rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px; margin-bottom: 1.25rem;' }, [
      el('span', { style: 'font-size: 11px; font-weight: 700; color: #004482; background: #e0f2fe; padding: 0.15rem 0.5rem; border-radius: 3px;' }, `MÃ THỦ TỤC: TTHC-${proc.id}`),
      el('h2', { style: 'font-family: var(--font-heading); font-size: 17px; font-weight: 800; color: #004482; margin: 0.35rem 0 0;' }, proc.name),
    ]);

    goToStep(1);
    mainArea.replaceChildren(headerCard, stepperBar, errorAlert, contentBox, navBar);
  }

  function renderSuccessReceipt(app, phone, selectedPaymentMethod = null) {
    const code = app.id || app.maHSXL;
    const rawData = app.data || app.dulieu || {};
    const totalFee = Number(app.fee ?? app.lePhi ?? 0);
    const paymentMethod = selectedPaymentMethod || app.payment_status?.method || rawData.payment_method || 'online';
    const isPaid = Boolean(app.payment_status?.is_paid);
    const receiptCard = el('div', { class: 'card', style: 'padding: 2.5rem; max-width: 680px; margin: 0 auto; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;' }, [
      el('div', { style: 'text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 1.5rem; margin-bottom: 1.5rem;' }, [
        el('div', { style: 'font-size: 32px; color: #16a34a; margin-bottom: 0.5rem;' }, '✓'),
        el('h2', { style: 'font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: #004482; margin: 0 0 0.5rem;' }, 'NỘP HỒ SƠ TRỰC TUYẾN THÀNH CÔNG'),
        el('p', { style: 'font-size: 13px; color: #475569; margin: 0;' }, 'Hồ sơ đã được tiếp nhận trên Hệ thống Một cửa điện tử.'),
      ]),
      el('div', { style: 'background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1.25rem; margin-bottom: 1.5rem;' }, [
        el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;' }, [
          el('span', { style: 'font-size: 12px; font-weight: 600; color: #64748b;' }, 'MÃ TIẾP NHẬN:'),
          el('strong', { class: 'numeric-data', style: 'font-size: 16px; color: #b91c1c;' }, code),
        ]),
        el('div', { style: 'display: flex; flex-direction: column; gap: 0.5rem; font-size: 13px;' }, [
          el('div', { style: 'display: flex; justify-content: space-between;' }, [el('span', { style: 'color: #64748b;' }, 'Thủ tục:'), el('strong', { style: 'text-align: right; max-width: 65%;' }, app.procedure_name)]),
          el('div', { style: 'display: flex; justify-content: space-between;' }, [el('span', { style: 'color: #64748b;' }, 'Người nộp:'), el('strong', {}, app.applicant_name)]),
          el('div', { style: 'display: flex; justify-content: space-between;' }, [el('span', { style: 'color: #64748b;' }, 'Trạng thái:'), el('span', { style: 'color: #0284c7; font-weight: 700;' }, app.status?.name || 'Chờ tiếp nhận')]),
          el('div', { style: 'display: flex; justify-content: space-between;' }, [el('span', { style: 'color: #64748b;' }, 'Lệ phí:'), el('strong', { class: 'numeric-data', style: 'color: #004482;' }, totalFee ? `${totalFee.toLocaleString('vi-VN')} VNĐ` : '0 VNĐ')]),
        ]),
      ]),
      ...createReceiptPaymentSection({ code, totalFee, paymentMethod, isPaid }),
      el('div', { style: 'display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;' }, [
        el('button', { type: 'button', class: 'btn btn-primary', style: 'background: #004482; font-weight: 700;', onClick: () => navigate(`/tra-cuu?code=${encodeURIComponent(code)}&phone=${encodeURIComponent(phone)}`) }, 'Tra cứu tiến độ hồ sơ này'),
        el('button', { type: 'button', class: 'btn btn-secondary', onClick: () => navigate('/thu-tuc') }, 'Nộp hồ sơ khác'),
      ]),
    ]);
    mainArea.replaceChildren(receiptCard);
  }

  function createReceiptPaymentSection({ code, totalFee, paymentMethod, isPaid }) {
    if (totalFee <= 0 || isPaid) return isPaid ? [el('div', { style: 'margin-bottom: 1.5rem; padding: 1rem 1.25rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; color: #166534; font-size: 13px; font-weight: 700;' }, 'Thanh toán đã được ghi nhận.') ] : [];

    if (paymentMethod === 'direct') {
      return [el('div', { style: 'margin-bottom: 1.5rem; padding: 1rem 1.25rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; color: #92400e; font-size: 13px; line-height: 1.55;' }, [
        el('strong', { style: 'display: block; margin-bottom: 0.35rem;' }, 'Chờ thanh toán trực tiếp tại quầy'),
        `Công dân mang mã hồ sơ ${code} và số tiền ${totalFee.toLocaleString('vi-VN')} VNĐ đến Bộ phận Một cửa. Cán bộ sẽ xác nhận thu tiền trên hệ thống.`,
      ])];
    }

    const qrBody = el('div', { style: 'display: flex; flex-direction: column; align-items: center; gap: 0.75rem; min-height: 110px; justify-content: center;' }, [
      el('span', { style: 'font-size: 12.5px; color: #64748b;' }, 'Đang tạo mã QR thanh toán...'),
    ]);
    const reloadButton = el('button', { type: 'button', class: 'btn btn-secondary btn-sm', style: 'border: 1px solid #93c5fd; color: #004482; font-weight: 700;' }, 'Tải lại mã QR');
    let stopPaymentPolling = () => {};
    const paymentSection = el('div', { style: 'margin-bottom: 1.5rem; border: 1px dashed #004482; border-radius: 6px; padding: 1.5rem; background: #f0f7ff; text-align: center;' }, [
      el('h4', { style: 'font-size: 14px; font-weight: 800; color: #004482; margin: 0 0 1rem; text-transform: uppercase;' }, 'THANH TOÁN TRỰC TUYẾN QUA PAYOS'),
      qrBody,
      el('p', { style: 'margin: 0.75rem 0 1rem; font-size: 12px; color: #475569; line-height: 1.5;' }, `Số tiền cần thanh toán: ${totalFee.toLocaleString('vi-VN')} VNĐ · Mã hồ sơ: ${code}`),
      reloadButton,
    ]);

    async function loadCheckout() {
      stopPaymentPolling();
      reloadButton.disabled = true;
      reloadButton.textContent = 'Tải lại mã QR';
      qrBody.replaceChildren(el('span', { style: 'font-size: 12.5px; color: #64748b;' }, 'Đang tạo mã QR thanh toán...'));
      try {
        const checkout = await createApplicationCheckout(api, code);
        const qrDataUrl = await renderPaymentQr(checkout.qr_code);
        qrBody.replaceChildren(
          el('img', {
            src: qrDataUrl,
            alt: `Mã QR thanh toán hồ sơ ${code}`,
            style: 'width: 240px; max-width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; background: #ffffff; padding: 0.35rem;',
          }),
          el('span', { style: 'font-size: 12px; color: #475569;' }, 'Mở ứng dụng ngân hàng để quét mã và hoàn tất thanh toán.'),
          el('a', { href: checkout.checkout_url, target: '_blank', rel: 'noreferrer', style: 'font-size: 12px; font-weight: 700; color: #004482;' }, 'Mở trang thanh toán PayOS'),
        );
        stopPaymentPolling = pollPaymentIntent(api, checkout.intent_id, {
          onStatus: (status) => {
            if (status?.status === 'paid') {
              stopPaymentPolling();
              paymentSection.style.background = '#f0fdf4';
              paymentSection.style.borderColor = '#86efac';
              qrBody.replaceChildren(
                el('strong', { style: 'color: #166534; font-size: 15px;' }, '✓ Đã thanh toán thành công'),
                el('span', { style: 'font-size: 12px; color: #166534;' }, 'Hồ sơ đã đủ điều kiện vào hàng chờ tiếp nhận.'),
              );
              reloadButton.disabled = true;
              reloadButton.textContent = 'Đã thanh toán';
            }
          },
        });
      } catch (error) {
        qrBody.replaceChildren(
          el('span', { style: 'font-size: 12.5px; color: #b91c1c; font-weight: 700;' }, error.message || 'Không thể tạo mã QR thanh toán.'),
        );
      } finally {
        reloadButton.disabled = false;
      }
    }

    reloadButton.addEventListener('click', loadCheckout);
    loadCheckout();
    return [paymentSection];
  }

  checkAuthAndRender();
  return container;
}
