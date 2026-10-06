import { el } from './dom.js';
import { api, getStoredUser, setStoredUser } from '../api/client.js';
import { showToast } from './toast.js';
import { openPasswordOtpModal } from './password-otp-modal.js';

export function createProfileAccountTab({ user, onProfileUpdated }) {
  const container = el('div', { class: 'profile-tab-content' });

  const inputStyle = 'width: 100%; padding: 0.5rem 0.75rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 5px; outline: none; transition: border-color 0.15s ease; box-sizing: border-box;';
  const labelStyle = 'display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 0.35rem;';

  function formField(label, inputNode, hint = null) {
    return el('div', { style: 'display: flex; flex-direction: column;' }, [
      el('label', { style: labelStyle }, label),
      inputNode,
      hint ? el('span', { style: 'font-size: 11px; color: #64748b; margin-top: 0.25rem;' }, hint) : null,
    ]);
  }

  const nameInput = el('input', { type: 'text', class: 'input', value: user?.full_name || user?.hoTen || '', style: inputStyle, placeholder: 'Nguyễn Văn A' });
  const emailInput = el('input', { type: 'email', class: 'input', value: user?.email || '', style: `${inputStyle} background: #f8fafc; cursor: not-allowed;`, disabled: true });
  const phoneInput = el('input', { type: 'tel', class: 'input', value: user?.phone || user?.soDienThoai || '', style: inputStyle, placeholder: '0912345678' });
  const cccdInput = el('input', { type: 'text', class: 'input', value: user?.citizen_id || user?.maCCCD || '', style: inputStyle, placeholder: 'Số căn cước 12 số' });

  const genderSelect = el('select', { class: 'input', style: inputStyle }, [
    el('option', { value: '' }, '-- Chọn giới tính --'),
    el('option', { value: 'Nam', selected: user?.gender === 'Nam' || user?.gioiTinh === 'Nam' }, 'Nam'),
    el('option', { value: 'Nữ', selected: user?.gender === 'Nữ' || user?.gioiTinh === 'Nữ' }, 'Nữ'),
  ]);

  const birthInput = el('input', { type: 'date', class: 'input', value: (user?.birth_date || user?.ngaySinh || '').slice(0, 10), style: inputStyle });
  const hometownInput = el('input', { type: 'text', class: 'input', value: user?.hometown || user?.queQuan || '', style: inputStyle, placeholder: 'Xã/Phường, Huyện/Quận, Tỉnh/TP' });
  const permanentInput = el('input', { type: 'text', class: 'input', value: user?.permanent_address || user?.noiThuongTru || '', style: inputStyle, placeholder: 'Địa chỉ thường trú' });
  const temporaryInput = el('input', { type: 'text', class: 'input', value: user?.temporary_address || user?.noiTamTru || '', style: inputStyle, placeholder: 'Địa chỉ tạm trú (nếu có)' });

  const saveBtn = el('button', {
    type: 'button',
    class: 'btn btn-primary',
    style: 'background: #004482; font-weight: 700; height: 38px; padding: 0 1.5rem; display: inline-flex; align-items: center; gap: 0.45rem;',
    onClick: handleSave,
  }, [
    el('span', { class: 'material-symbols-outlined', style: 'font-size: 18px;' }, 'save'),
    el('span', {}, 'Lưu thông tin'),
  ]);

  const curPwdInput = el('input', { type: 'password', class: 'input', style: inputStyle, placeholder: 'Nhập mật khẩu hiện tại...' });
  const newPwdInput = el('input', { type: 'password', class: 'input', style: inputStyle, placeholder: 'Mật khẩu mới (từ 8 ký tự)...' });
  const cfmPwdInput = el('input', { type: 'password', class: 'input', style: inputStyle, placeholder: 'Nhập lại mật khẩu mới...' });

  const changePwdBtn = el('button', {
    type: 'button',
    class: 'btn btn-secondary',
    style: 'border: 1px solid #cbd5e1; font-weight: 700; height: 36px; padding: 0 1.25rem; display: inline-flex; align-items: center; gap: 0.4rem; color: #004482;',
    onClick: handleChangePassword,
  }, [
    el('span', { class: 'material-symbols-outlined', style: 'font-size: 18px;' }, 'lock_reset'),
    el('span', {}, 'Đổi mật khẩu'),
  ]);

  const passwordCard = el('div', {
    class: 'card',
    style: 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(15,23,42,0.03);',
  }, [
    el('h3', { style: 'font-size: 14px; font-weight: 700; color: #004482; margin: 0 0 1.25rem; display: flex; align-items: center; gap: 0.45rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.65rem;' }, [
      el('span', { class: 'material-symbols-outlined', style: 'font-size: 18px;' }, 'security'),
      '3. Đổi mật khẩu tài khoản',
    ]),
    el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1rem;' }, [
      formField('Mật khẩu hiện tại (*)', curPwdInput),
      formField('Mật khẩu mới (*)', newPwdInput),
      formField('Xác nhận mật khẩu mới (*)', cfmPwdInput),
    ]),
    el('div', { style: 'display: flex; justify-content: flex-end; margin-top: 0.75rem;' }, [changePwdBtn]),
  ]);

  const contactCard = el('div', {
    class: 'card',
    style: 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.25rem; box-shadow: 0 1px 3px rgba(15,23,42,0.03);',
  }, [
    el('h3', { style: 'font-size: 14px; font-weight: 700; color: #004482; margin: 0 0 1.25rem; display: flex; align-items: center; gap: 0.45rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.65rem;' }, [
      el('span', { class: 'material-symbols-outlined', style: 'font-size: 18px;' }, 'contact_mail'),
      '1. Thông tin liên hệ cơ bản',
    ]),
    el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;' }, [
      formField('Họ và tên (*)', nameInput),
      formField('Địa chỉ Email', emailInput),
      formField('Số điện thoại (*)', phoneInput),
    ]),
  ]);

  const identityCard = el('div', {
    class: 'card',
    style: 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(15,23,42,0.03);',
  }, [
    el('h3', { style: 'font-size: 14px; font-weight: 700; color: #004482; margin: 0 0 1.25rem; display: flex; align-items: center; gap: 0.45rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.65rem;' }, [
      el('span', { class: 'material-symbols-outlined', style: 'font-size: 18px;' }, 'badge'),
      '2. Thông tin định danh & Cư trú',
    ]),
    el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1rem;' }, [
      formField('Số CCCD / Định danh cá nhân', cccdInput),
      formField('Giới tính', genderSelect),
      formField('Ngày sinh', birthInput),
    ]),
    el('div', { style: 'display: flex; flex-direction: column; gap: 1rem;' }, [
      formField('Quê quán', hometownInput),
      formField('Nơi thường trú', permanentInput),
      formField('Nơi tạm trú', temporaryInput),
    ]),
  ]);

  const actionsRow = el('div', { style: 'display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 0.5rem;' }, [saveBtn]);

  container.append(contactCard, identityCard, passwordCard, actionsRow);

  loadFullProfile();

  async function loadFullProfile() {
    try {
      const res = await api.get('/citizen/profile');
      if (res?.data) populateFields(res.data);
    } catch (_) {
      try {
        const meRes = await api.get('/auth/me');
        if (meRes?.data) populateFields(meRes.data);
      } catch (_) {}
    }
  }

  function populateFields(d) {
    if (d.full_name || d.hoTen) nameInput.value = d.full_name || d.hoTen;
    if (d.email) emailInput.value = d.email;
    if (d.phone || d.soDienThoai) phoneInput.value = d.phone || d.soDienThoai;
    if (d.citizen_id || d.maCCCD) cccdInput.value = d.citizen_id || d.maCCCD;
    if (d.gender || d.gioiTinh) genderSelect.value = d.gender || d.gioiTinh;
    if (d.birth_date || d.ngaySinh) birthInput.value = (d.birth_date || d.ngaySinh).slice(0, 10);
    if (d.hometown || d.queQuan) hometownInput.value = d.hometown || d.queQuan;
    if (d.permanent_address || d.noiThuongTru) permanentInput.value = d.permanent_address || d.noiThuongTru;
    if (d.temporary_address || d.noiTamTru) temporaryInput.value = d.temporary_address || d.noiTamTru;
  }

  async function handleSave() {
    const fullName = nameInput.value.trim();
    const phone = phoneInput.value.trim();
    if (!fullName) return showToast.error('Vui lòng nhập họ và tên.');
    if (!phone || phone.length < 9) return showToast.error('Vui lòng nhập số điện thoại hợp lệ (9-10 chữ số).');

    saveBtn.disabled = true;
    saveBtn.textContent = 'Đang lưu thay đổi...';

    try {
      await api.patch('/citizen/profile', { full_name: fullName, phone });
      await api.patch('/citizen/identity', {
        citizen_id: cccdInput.value.trim() || null,
        gender: genderSelect.value || null,
        birth_date: birthInput.value || null,
        hometown: hometownInput.value.trim() || null,
        permanent_address: permanentInput.value.trim() || null,
        temporary_address: temporaryInput.value.trim() || null,
      });

      const updated = {
        ...getStoredUser(),
        full_name: fullName,
        hoTen: fullName,
        phone,
        soDienThoai: phone,
        citizen_id: cccdInput.value.trim(),
        maCCCD: cccdInput.value.trim(),
      };
      setStoredUser(updated);
      window.dispatchEvent(new CustomEvent('auth:change', { detail: updated }));
      showToast.success('Cập nhật thông tin tài khoản thành công!');
      if (typeof onProfileUpdated === 'function') onProfileUpdated(updated);
    } catch (err) {
      const msg = err?.data?.errors?.message || err?.data?.message || err?.message || 'Không thể cập nhật hồ sơ.';
      showToast.error(msg);
    } finally {
      saveBtn.disabled = false;
      saveBtn.replaceChildren(
        el('span', { class: 'material-symbols-outlined', style: 'font-size: 18px;' }, 'save'),
        el('span', {}, 'Lưu thông tin')
      );
    }
  }

  async function handleChangePassword() {
    const cur = curPwdInput.value;
    const next = newPwdInput.value;
    const cfm = cfmPwdInput.value;

    if (!cur) return showToast.error('Vui lòng nhập mật khẩu hiện tại.');
    if (!next || next.length < 8) return showToast.error('Mật khẩu mới phải có tối thiểu 8 ký tự.');
    if (next !== cfm) return showToast.error('Xác nhận mật khẩu mới không khớp.');

    changePwdBtn.disabled = true;
    changePwdBtn.textContent = 'Đang gửi mã OTP...';

    try {
      const res = await api.post('/citizen/password/otp', { current_password: cur });
      openPasswordOtpModal({
        challenge: res.data,
        newPassword: next,
        onSuccess: () => {
          showToast.success('Đổi mật khẩu tài khoản thành công!');
          curPwdInput.value = '';
          newPwdInput.value = '';
          cfmPwdInput.value = '';
        },
      });
    } catch (err) {
      const msg = err?.data?.message || err?.data?.errors?.message || err?.message || 'Không thể gửi yêu cầu đổi mật khẩu.';
      showToast.error(msg);
    } finally {
      changePwdBtn.disabled = false;
      changePwdBtn.replaceChildren(
        el('span', { class: 'material-symbols-outlined', style: 'font-size: 18px;' }, 'lock_reset'),
        el('span', {}, 'Đổi mật khẩu')
      );
    }
  }

  return container;
}
