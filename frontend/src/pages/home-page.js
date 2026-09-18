import { el } from '../components/dom.js';
import { renderHomeHero } from './home-hero.js';
import { renderHomeBentoAndKpi } from './home-bento-kpi.js';
import { renderHomeFields } from './home-fields.js';
import { renderHomePopularProcedures } from './home-popular.js';

/**
 * renderHomePage — Main Public Portal Landing Page
 * Pixel-perfect implementation matching the institutional modernism specification.
 *
 * @param {object} context
 * @param {Function} context.navigate
 * @returns {HTMLElement}
 */
export function renderHomePage({ navigate }) {
  const container = el('div', { class: 'page-home' });

  const hero = renderHomeHero({ navigate });
  const bentoAndKpi = renderHomeBentoAndKpi({ navigate });
  const fields = renderHomeFields({ navigate });
  const popular = renderHomePopularProcedures({ navigate });

  container.append(hero, bentoAndKpi, fields, popular);
  return container;
}
