/**
 * SPA Router — Lightweight client-side router
 */
export class Router {
  constructor(routes = {}, rootEl, options = {}) {
    this.routes = routes;
    this.rootEl = rootEl;
    this.onRouteChange = options.onRouteChange || null;
    this.currentPath = '';
    this.currentTeardown = null;

    window.addEventListener('popstate', () => {
      this.resolve(currentBrowserPath());
    });

    document.addEventListener('click', (e) => {
      const link = e.target.closest('a[href^="/"]');
      if (link && !link.target && !e.ctrlKey && !e.metaKey) {
        e.preventDefault();
        this.navigate(link.getAttribute('href'));
      }
    });
  }

  navigate(path) {
    if (this.currentPath === path) return;
    window.history.pushState({}, '', path);
    this.resolve(path);
  }

  resolve(path = window.location.pathname + window.location.search) {
    this.currentPath = path;

    if (typeof this.currentTeardown === 'function') {
      try { this.currentTeardown(); } catch (err) { console.error('Teardown error:', err); }
      this.currentTeardown = null;
    }

    const [pathname, searchStr = ''] = path.split('?');
    const searchParams = new URLSearchParams(searchStr);

    let matchedHandler = this.routes[pathname];
    let params = {};

    if (!matchedHandler) {
      for (const [routePattern, handler] of Object.entries(this.routes)) {
        if (routePattern.includes(':')) {
          const regexStr = '^' + routePattern.replace(/:([a-zA-Z0-9_]+)/g, '([^/]+)') + '$';
          const match = pathname.match(new RegExp(regexStr));
          if (match) {
            matchedHandler = handler;
            const paramNames = (routePattern.match(/:([a-zA-Z0-9_]+)/g) || []).map(p => p.slice(1));
            paramNames.forEach((name, idx) => {
              params[name] = match[idx + 1];
            });
            break;
          }
        }
      }
    }

    if (!matchedHandler && this.routes['*']) {
      matchedHandler = this.routes['*'];
    }

    if (matchedHandler && this.rootEl) {
      const rendered = matchedHandler({
        path,
        pathname,
        params,
        searchParams,
        navigate: this.navigate.bind(this),
      });

      if (rendered instanceof HTMLElement) {
        this.rootEl.replaceChildren(rendered);
      } else if (rendered && typeof rendered.mount === 'function') {
        this.rootEl.replaceChildren(rendered.element);
        this.currentTeardown = rendered.teardown;
      }
    }

    document.querySelectorAll('.nav-link').forEach((link) => {
      const href = link.getAttribute('href');
      link.classList.toggle('active', href === pathname || (href !== '/' && pathname.startsWith(href)));
    });

    if (typeof this.onRouteChange === 'function') {
      try {
        this.onRouteChange({ path, pathname, params, searchParams });
      } catch (err) {
        console.error('onRouteChange error:', err);
      }
    }

    window.scrollTo(0, 0);
  }

  start() {
    this.resolve(currentBrowserPath());
  }
}

export function currentBrowserPath(location = globalThis.window?.location) {
  if (!location) return '/';
  return `${location.pathname || '/'}${location.search || ''}${location.hash || ''}`;
}
