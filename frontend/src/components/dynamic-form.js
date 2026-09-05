import { el } from './dom.js';

export function createDynamicForm(formConfig, user = {}) {
  let groups = [];
  try {
    const parsed = typeof formConfig === 'string' ? JSON.parse(formConfig) : formConfig;
    groups = Array.isArray(parsed) ? parsed : (parsed?.groups || []);
  } catch (e) {
    console.error('Invalid form config JSON:', e);
    groups = [];
  }

  const fieldRegistry = [];
  const formContainer = el('div', { class: 'dynamic-form-wrapper', style: 'display: flex; flex-direction: column; gap: 1.5rem;' });

  function getAutoFillValue(f) {
    const n = (f.name || '').toLowerCase();
    if (n.includes('ho_ten') || n === 'ten' || n.includes('nguoi_nop')) {
      return user.full_name || user.hoTen || user.name || '';
    }
    if (n.includes('so_dien_thoai') || n.includes('phone') || n === 'sdt') {
      return user.phone || user.so_dien_thoai || user.soDienThoai || '';
    }
    if (n.includes('email')) {
      return user.email || '';
    }
    if (n.includes('cccd') || n.includes('cmnd') || n === 'so_giay_to') {
      return user.citizen_id || user.so_cccd || '';
    }
    return f.default_value || '';
  }

  function renderFieldControl(f) {
    const type = f.type || 'text';
    const autoVal = getAutoFillValue(f);
    let controlEl;
    let getValue;

    if (type === 'select') {
      controlEl = el('select', { class: 'input', name: f.name });
      controlEl.append(el('option', { value: '' }, `-- Chọn ${f.label || 'giá trị'} --`));
      (f.options || []).forEach(opt => {
        const val = typeof opt === 'object' ? (opt.value ?? opt.id ?? '') : String(opt);
        const text = typeof opt === 'object' ? (opt.label ?? opt.name ?? '') : String(opt);
        const optEl = el('option', { value: val }, text);
        if (val === 'Căn cước công dân' || val === 'Việt Nam') optEl.selected = true;
        controlEl.append(optEl);
      });
      getValue = () => controlEl.value;
    } else if (type === 'radio') {
      const radioGroup = el('div', { style: 'display: flex; gap: 1rem; flex-wrap: wrap; padding: 0.35rem 0;' });
      const options = f.options || ['Có', 'Không'];
      let selectedVal = options[0] || '';
      options.forEach((opt, idx) => {
        const radioId = `radio_${f.name}_${idx}`;
        const input = el('input', { type: 'radio', id: radioId, name: f.name, value: opt, checked: idx === 0 });
        input.addEventListener('change', () => { selectedVal = opt; });
        radioGroup.append(el('label', { for: radioId, style: 'display: inline-flex; align-items: center; gap: 0.35rem; font-size: 13px; cursor: pointer;' }, [input, el('span', {}, opt)]));
      });
      controlEl = radioGroup;
      getValue = () => selectedVal;
    } else if (type === 'textarea') {
      controlEl = el('textarea', { class: 'input', name: f.name, rows: '3', style: 'resize: vertical;' }, autoVal);
      getValue = () => controlEl.value;
    } else {
      const inputType = type === 'date' ? 'date' : (type === 'number' ? 'number' : (type === 'email' ? 'email' : 'text'));
      controlEl = el('input', {
        type: inputType,
        class: `input ${type === 'number' ? 'numeric-data' : ''}`,
        name: f.name,
        value: autoVal,
        placeholder: f.placeholder || '',
      });
      if (f.required) controlEl.required = true;
      getValue = () => controlEl.value;
    }

    fieldRegistry.push({ name: f.name, label: f.label, required: !!f.required, getValue, element: controlEl });

    const fieldWrapper = el('div', { class: 'form-field-group', style: 'display: flex; flex-direction: column; gap: 0.25rem;' });
    const labelRow = el('label', { style: 'font-size: 12px; font-weight: 700; color: #0f172a;' }, [
      el('span', {}, f.label || f.name),
      f.required ? el('span', { style: 'color: #b91c1c; margin-left: 2px;' }, '*') : '',
    ]);

    fieldWrapper.append(labelRow, controlEl);
    return fieldWrapper;
  }

  function renderRow(row) {
    const cols = row.columns || [];
    const gridEl = el('div', {
      class: 'form-row-grid',
      style: `display: grid; grid-template-columns: repeat(auto-fit, minmax(${cols.length > 3 ? '200px' : '240px'}, 1fr)); gap: 0.85rem; margin-bottom: 0.85rem;`,
    });
    cols.forEach(colField => {
      gridEl.append(renderFieldControl(colField));
    });
    return gridEl;
  }

  groups.forEach((g, gIdx) => {
    if (!g || !g.fields || g.fields.length === 0) return;
    const groupCard = el('div', {
      class: 'card dynamic-group-card',
      style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;',
    });

    const groupTitle = el('h3', {
      style: 'font-family: var(--font-heading); font-size: 14px; font-weight: 700; color: #004482; margin: 0 0 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;',
    }, [
      el('span', { style: 'font-size: 12px; background: #e0f2fe; color: #004482; padding: 0.15rem 0.45rem; border-radius: 3px; font-weight: 800;' }, `${gIdx + 1}`),
      el('span', {}, g.group || `Nhóm thông tin ${gIdx + 1}`),
    ]);

    groupCard.append(groupTitle);

    const fieldsContainer = el('div', { style: 'display: flex; flex-direction: column;' });
    g.fields.forEach(f => {
      if (f.type === 'row') {
        fieldsContainer.append(renderRow(f));
      } else {
        const singleField = renderFieldControl(f);
        singleField.style.marginBottom = '0.85rem';
        fieldsContainer.append(singleField);
      }
    });

    groupCard.append(fieldsContainer);
    formContainer.append(groupCard);
  });

  return {
    element: formContainer,
    getValues: () => {
      const data = {};
      fieldRegistry.forEach(item => {
        if (item.name) {
          data[item.name] = item.getValue();
        }
      });
      return data;
    },
    validate: () => {
      for (const item of fieldRegistry) {
        if (item.required) {
          const val = item.getValue();
          if (val === undefined || val === null || String(val).trim() === '') {
            return { valid: false, message: `Vui lòng nhập/chọn: ${item.label || item.name}` };
          }
        }
      }
      return { valid: true };
    },
  };
}
