/**
 * el — Lightweight DOM Element Builder
 *
 * @param {string} tag
 * @param {Record<string, any>} [props]
 * @param {Array<HTMLElement|string|null|undefined>} [children]
 * @returns {HTMLElement}
 */
export function el(tag, props = {}, ...children) {
  const element = document.createElement(tag);

  if (props) {
    for (const [key, value] of Object.entries(props)) {
      if (value === null || value === undefined) {
        continue;
      }
      if (key === 'class' || key === 'className') {
        element.className = String(value);
      } else if (key === 'style' && typeof value === 'object') {
        Object.assign(element.style, value);
      } else if (key === 'style' && typeof value === 'string') {
        element.style.cssText = value;
      } else if (key.startsWith('on') && typeof value === 'function') {
        const eventName = key.slice(2).toLowerCase();
        element.addEventListener(eventName, value);
      } else if (key === 'dataset' && typeof value === 'object') {
        Object.assign(element.dataset, value);
      } else if (typeof value === 'boolean') {
        if (value) {
          element.setAttribute(key, '');
          element[key] = true;
        } else {
          element.removeAttribute(key);
          element[key] = false;
        }
      } else {
        element.setAttribute(key, String(value));
        if (key in element) {
          try { element[key] = value; } catch { /* read-only */ }
        }
      }
    }
  }

  appendChildren(element, children);
  return element;
}

function appendChildren(parent, children) {
  for (const child of children.flat(Infinity)) {
    if (child === null || child === undefined || child === false) {
      continue;
    }
    if (child instanceof Node) {
      parent.appendChild(child);
    } else {
      parent.appendChild(document.createTextNode(String(child)));
    }
  }
}
