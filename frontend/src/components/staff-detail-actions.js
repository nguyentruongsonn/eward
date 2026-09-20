import { el } from './dom.js';
import { api } from '../api/client.js';
import { showToast } from './toast.js';
import { openStaffCompletionModal } from './staff-completion-modal.js';
import { openStaffLeaderApprovalModal } from './staff-leader-approval-modal.js';
import { openStaffActionModal } from './staff-action-modal.js';
import { openStaffCounterPaymentModal } from './staff-counter-payment-modal.js';

export function createStaffActionButtons({ app, role, handleAction, loadDetail }) {
  const isCaseOfficer = role === 'case-officer';
  const isLeader = role === 'leader';
  const sid = Number(app.status?.id ?? 1);

  const printBtn = el('button', {
    type: 'button', class: 'btn btn-secondary btn-sm',
    style: 'height: 32px; font-size: 12.5px; font-weight: 700; border: 1px solid #cbd5e1; padding: 0 0.85rem; background: #ffffff; color: #334155;',
    onClick: () => window.print(),
  }, 'In phiếu');

  const storageBtn = el('button', {
    type: 'button', class: 'btn btn-secondary btn-sm',
    style: 'height: 32px; font-size: 12.5px; font-weight: 700; border: 1px solid #cbd5e1; padding: 0 0.85rem; background: #ffffff; color: #004482; transition: all 0.2s ease;',
    onClick: async () => {
      storageBtn.disabled = true;
      storageBtn.textContent = 'Đang lưu kho...';
      try {
        await api.post(`/admin/applications/${app.id}/comments`, { comment: 'Đã lưu hồ sơ vào kho xử lý.' });
        showToast.success(`Đã lưu hồ sơ ${app.id} vào kho xử lý.`);
        storageBtn.textContent = '✓ Đã lưu vào kho';
        storageBtn.style.color = '#15803d';
        storageBtn.style.borderColor = '#86efac';
        loadDetail();
      } catch (err) {
        showToast.error(err.message || 'Lỗi lưu kho');
        storageBtn.disabled = false;
        storageBtn.textContent = 'Lưu vào kho';
      }
    },
  }, 'Lưu vào kho');

  if (isLeader) {
    const isApproved = sid === 9 || sid === 10;
    const isRework = sid === 12;

    const approveBtn = el('button', {
      type: 'button',
      class: (isApproved || isRework) ? 'btn btn-secondary btn-sm' : 'btn btn-primary btn-sm',
      disabled: isApproved || isRework,
      title: isRework ? 'Hồ sơ đang chờ chuyên viên thụ lý xử lý lại' : (isApproved ? 'Hồ sơ đã được phê duyệt hoàn thành' : ''),
      style: (isApproved || isRework)
        ? 'background: #f1f5f9; color: #94a3b8; font-weight: 700; height: 32px; font-size: 12.5px; border: 1px solid #cbd5e1; padding: 0 0.85rem; cursor: not-allowed;'
        : 'background: #004482; color: #ffffff; font-weight: 700; height: 32px; font-size: 12.5px; border: none; padding: 0 0.85rem; cursor: pointer;',
      onClick: () => {
        approveBtn.disabled = true;
        reworkBtn.disabled = true;
        openStaffLeaderApprovalModal({
          app,
          onSubmitted: () => {
            showToast.success('Đã phê duyệt hoàn thành hồ sơ thành công!');
            loadDetail();
          },
        });
      },
    }, isApproved ? '✓ Đã phê duyệt hoàn thành' : 'Xác nhận hoàn thành (phê duyệt)');

    const reworkBtn = el('button', {
      type: 'button', class: 'btn btn-sm',
      disabled: isRework,
      title: isRework ? 'Đã gửi yêu cầu xử lý lại' : (isApproved ? 'Hoàn lại thao tác phê duyệt & chuyển cán bộ thụ lý xử lý lại' : ''),
      style: isRework
        ? 'background: #f8fafc; color: #94a3b8; border: 1px solid #cbd5e1; font-weight: 700; height: 32px; font-size: 12.5px; padding: 0 0.85rem; cursor: not-allowed;'
        : (isApproved
          ? 'background: #fff1f2; color: #b91c1c; border: 1px solid #fca5a5; font-weight: 700; height: 32px; font-size: 12.5px; padding: 0 0.85rem; cursor: pointer;'
          : 'background: #ffffff; color: #b91c1c; border: 1px solid #fca5a5; font-weight: 700; height: 32px; font-size: 12.5px; padding: 0 0.85rem; cursor: pointer;'),
      onClick: () => {
        approveBtn.disabled = true;
        reworkBtn.disabled = true;
        openStaffActionModal({
          action: 'rework',
          actionLabel: isApproved ? 'Hoàn lại thao tác phê duyệt' : 'Yêu cầu xử lý lại',
          onSubmit: async (p) => {
            await handleAction('rework', p);
            const msg = isApproved
              ? `Lãnh đạo hoàn lại thao tác phê duyệt, yêu cầu thụ lý lại: "${p?.note || ''}"`
              : `Lãnh đạo yêu cầu xử lý lại: "${p?.note || ''}"`;
            await api.post(`/admin/applications/${app.id}/comments`, { content: msg, comment: msg }).catch(() => {});
            showToast.warning(isApproved ? 'Đã hoàn lại phê duyệt và chuyển hồ sơ về cán bộ thụ lý!' : 'Đã gửi yêu cầu xử lý lại cho Cán bộ thụ lý!');
          },
        });
      },
    }, isRework ? '✓ Đã yêu cầu xử lý lại' : (isApproved ? 'Hoàn lại thao tác (Yêu cầu xử lý lại)' : 'Yêu cầu xử lý lại'));

    return [approveBtn, reworkBtn, storageBtn, printBtn];
  }

  if (isCaseOfficer) {
    const isForwarded = sid === 4;
    const isSupplement = sid === 5;
    const isRework = sid === 12;

    const completionBtn = el('button', {
      type: 'button',
      class: (isForwarded || isSupplement) ? 'btn btn-secondary btn-sm' : 'btn btn-primary btn-sm',
      disabled: isForwarded || isSupplement,
      title: isForwarded
        ? 'Hồ sơ đã chuyển lãnh đạo phê duyệt'
        : (isSupplement ? 'Hồ sơ đang chờ công dân bổ sung giấy tờ' : ''),
      style: (isForwarded || isSupplement)
        ? 'background: #f1f5f9; color: #94a3b8; font-weight: 700; height: 32px; font-size: 12.5px; border: 1px solid #cbd5e1; padding: 0 0.85rem; cursor: not-allowed;'
        : 'background: #004482; color: #ffffff; font-weight: 700; height: 32px; font-size: 12.5px; border: none; padding: 0 0.85rem; cursor: pointer;',
      onClick: () => openStaffCompletionModal({
        app,
        onSubmitted: () => {
          showToast.success('Đã chuyển hồ sơ lên Lãnh đạo phê duyệt thành công!');
          loadDetail();
        },
      }),
    }, isForwarded ? '✓ Đã chuyển phê duyệt' : (isRework ? 'Xác nhận hoàn thành (Trình duyệt lại)' : 'Xác nhận hoàn thành'));

    const supplementBtn = el('button', {
      type: 'button',
      class: 'btn btn-secondary btn-sm',
      disabled: isForwarded || isSupplement,
      title: isForwarded
        ? 'Hồ sơ đã chuyển lãnh đạo phê duyệt, không thể yêu cầu bổ sung'
        : (isSupplement ? 'Hồ sơ đang chờ công dân bổ sung giấy tờ' : ''),
      style: (isForwarded || isSupplement)
        ? 'height: 32px; font-size: 12.5px; font-weight: 700; border: 1px solid #cbd5e1; padding: 0 0.85rem; background: #f1f5f9; color: #94a3b8; cursor: not-allowed;'
        : 'height: 32px; font-size: 12.5px; font-weight: 700; border: 1px solid #cbd5e1; padding: 0 0.85rem; background: #ffffff; color: #004482; cursor: pointer;',
      onClick: () => openStaffActionModal({
        action: 'requestSupplement',
        actionLabel: 'Yêu cầu bổ sung',
        procedureComponents: app.procedure_components || [],
        onSubmit: async (p) => {
          await handleAction('requestSupplement', p);
          showToast.info('Đã gửi yêu cầu bổ sung giấy tờ tới công dân.');
        },
      }),
    }, isSupplement ? '✓ Đang chờ bổ sung' : 'Yêu cầu bổ sung');

    return [completionBtn, supplementBtn, storageBtn, printBtn];
  }

  const actionDefs = [
    { id: 'accept', label: 'Tiếp nhận hồ sơ', primary: true },
    { id: 'confirmReception', label: 'Xác nhận tiếp nhận', primary: true },
    { id: 'forward', label: 'Chuyển thụ lý', primary: true },
    { id: 'approve', label: 'Phê duyệt hồ sơ', primary: true },
    { id: 'deliver', label: 'Trả kết quả', primary: true },
    { id: 'requestSupplement', label: 'Yêu cầu bổ sung', primary: false },
    { id: 'rework', label: 'Yêu cầu sửa lại', danger: true },
    { id: 'reject', label: 'Từ chối tiếp nhận', danger: true },
  ];
  const isOneStop = role === 'one-stop';
  const allowed = app.allowed_actions || [];
  const availableDefs = isOneStop
    ? actionDefs.filter(d => ['accept', 'confirmReception', 'requestSupplement', 'deliver'].includes(d.id))
    : actionDefs;
  const btns = availableDefs.filter(d => allowed.includes(d.id)).map(act => el('button', {
    type: 'button', class: 'btn btn-sm',
    style: act.danger
      ? 'background: #ffffff; color: #b91c1c; border: 1px solid #fca5a5; font-weight: 700; height: 32px; font-size: 12.5px; padding: 0 0.85rem;'
      : act.primary
        ? 'background: #004482; color: #ffffff; border: none; font-weight: 700; height: 32px; font-size: 12.5px; padding: 0 0.85rem;'
        : 'background: #ffffff; color: #004482; border: 1px solid #cbd5e1; font-weight: 700; height: 32px; font-size: 12.5px; padding: 0 0.85rem;',
    onClick: () => openStaffActionModal({
      action: act.id,
      actionLabel: act.label,
      procedureComponents: app.procedure_components || [],
      onSubmit: async (p) => {
        await handleAction(act.id, p);
        showToast.success(`Thực hiện "${act.label}" thành công.`);
      },
    }),
  }, act.label));
  if (isOneStop && sid === 13 && Number(app.fee || 0) > 0 && !app.payment_status?.is_paid) {
    btns.unshift(el('button', {
      type: 'button', class: 'btn btn-sm',
      style: 'background: #fffbeb; color: #92400e; border: 1px solid #fcd34d; font-weight: 700; height: 32px; font-size: 12.5px; padding: 0 0.85rem;',
      onClick: () => openStaffCounterPaymentModal({ application: app, onSubmitted: loadDetail }),
    }, 'Xác nhận thu trực tiếp'));
  }
  btns.push(printBtn);
  return btns;
}
