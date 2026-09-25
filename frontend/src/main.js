import { el } from './components/dom.js';
import { renderHeader } from './components/header.js';
import { renderFooter } from './components/footer.js';
import { renderChatAiWidget } from './components/chat-ai.js';
import { createStaffLayout } from './components/staff-layout.js';
import { Router } from './router/router.js';

import { renderHomePage } from './pages/home-page.js';
import { renderProceduresPage } from './pages/procedures-page.js';
import { renderTrackingPage } from './pages/tracking-page.js';
import { renderLoginPage } from './pages/login-page.js';
import { renderProcedureDetailPage } from './pages/procedure-detail-page.js';
import { renderSubmitApplicationPage } from './pages/submit-application-page.js';
import { renderStaffQueuePage } from './pages/staff-queue-page.js';
import { renderStaffDetailPage } from './pages/staff-detail-page.js';
import { renderStaffDashboardPage } from './pages/staff-dashboard-page.js';
import { renderStaffReportsPage } from './pages/staff-reports-page.js';
import { renderStaffProceduresPage } from './pages/staff-procedures-page.js';
import { renderAdminStaffPage } from './pages/admin-staff-page.js';
import { renderAdminUsersPage } from './pages/admin-users-page.js';
import { renderAdminFieldsPage } from './pages/admin-fields-page.js';
import { renderProfilePage } from './pages/profile-page.js';
import { renderPaymentReturnPage } from './pages/payment-return-page.js';

import { getStoredUser, getAuthToken } from './api/client.js';
import { normalizeStaffRole } from './components/staff-nav.js';
import { canAccessStaffRoute } from './components/staff-routes.js';
import { showToast } from './components/toast.js';

function initApp() {
  const root = document.getElementById('app');
  if (!root) return;

  const contentArea = el('main', { id: 'app-content', style: 'flex: 1; display: flex; flex-direction: column;' });

  const withStaffLayout = (pageHandler) => (ctx) => {
    const user = getStoredUser();
    const token = getAuthToken();
    const role = normalizeStaffRole(user?.vaiTro || user?.role);

    if (!token || !user || !canAccessStaffRoute(ctx.pathname, role)) {
      showToast.warning('Bạn không có quyền truy cập trang này. Đang chuyển về trang chủ.');
      ctx.navigate('/');
      return el('div', { style: 'padding: 2rem; text-align: center; color: #64748b;' }, 'Đang chuyển về trang chủ...');
    }

    const pageNode = pageHandler(ctx);
    const fullPath = ctx.pathname + (ctx.searchParams?.toString() ? `?${ctx.searchParams.toString()}` : '');
    return createStaffLayout({
      navigate: ctx.navigate,
      contentNode: pageNode,
      currentPath: fullPath,
    });
  };

  const header = renderHeader({ navigate: (p) => router.navigate(p) });
  const footer = renderFooter();
  const chatAi = renderChatAiWidget();

  const renderRootPage = (ctx) => (
    ctx.searchParams?.has('payment')
      ? renderPaymentReturnPage(ctx)
      : renderHomePage(ctx)
  );

  const router = new Router({
    '/': renderRootPage,
    '/thu-tuc': renderProceduresPage,
    '/thu-tuc/:id': renderProcedureDetailPage,
    '/nop-ho-so': renderSubmitApplicationPage,
    '/nop-ho-so/:id': renderSubmitApplicationPage,
    '/tra-cuu': renderTrackingPage,
    '/dang-nhap': renderLoginPage,
    '/tai-khoan': renderProfilePage,
    '/profile': renderProfilePage,
    '/can-bo': withStaffLayout((ctx) => {
      if (ctx.searchParams?.has('status')) {
        return renderStaffQueuePage(ctx);
      }
      return renderStaffDashboardPage(ctx);
    }),
    '/can-bo/ho-so': withStaffLayout(renderStaffQueuePage),
    '/can-bo/ho-so/:id': withStaffLayout(renderStaffDetailPage),
    '/can-bo/bao-cao': withStaffLayout(renderStaffReportsPage),
    '/can-bo/danh-muc-thu-tuc': withStaffLayout(renderStaffProceduresPage),
    '/can-bo/quan-tri/nguoi-dung': withStaffLayout(renderAdminUsersPage),
    '/can-bo/quan-tri/can-bo': withStaffLayout(renderAdminStaffPage),
    '/can-bo/quan-tri/linh-vuc': withStaffLayout(renderAdminFieldsPage),
    '/can-bo/tra-cuu': withStaffLayout(renderTrackingPage),
    '*': renderHomePage,
  }, contentArea, {
    onRouteChange: ({ pathname }) => {
      const isStaffRoute = pathname.startsWith('/can-bo');
      header.style.display = isStaffRoute ? 'none' : '';
      footer.style.display = isStaffRoute ? 'none' : '';
      chatAi.style.display = 'none';
    },
  });

  root.append(header, contentArea, footer, chatAi);
  router.start();
}

document.addEventListener('DOMContentLoaded', initApp);
