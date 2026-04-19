/* =============================================================
   CoderEmbassy Product Info — Admin SPA (Vanilla JS)
   ============================================================= */

(() => {
'use strict';
console.log('CoderEmbassy Product Info Admin loaded - v1.1.1');

const AJAX_URL = cmfwAjax.ajax_url;
const NONCE    = cmfwAjax.nonce;
const CURRENT_USER = cmfwAjax.current_user || { display_name: 'Admin' };
const IS_PRO   = cmfwAjax.pro_active === '1';

// ── Root (toasts and modal append here) ────────────────────
const root = document.getElementById('dpp-root');
if (!root) return; // Disarm if not on plugin page

const toastWrap = document.createElement('div');
toastWrap.className = 'dpp-toast-wrap';
document.body.appendChild(toastWrap);

function toast(msg, type = 'success') {
  const el = document.createElement('div');
  el.className = `dpp-toast${type === 'error' ? ' error' : ''}`;
  el.textContent = msg;
  toastWrap.appendChild(el);
  setTimeout(() => el.remove(), 3500);
}

// ── SVG icons ──────────────────────────────────────────────
const icons = {
  dashboard: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>`,
  settings:  `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>`,
  moon:      `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>`,
  sun:       `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>`,
  plus:      `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>`,
  trash:     `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>`,
  image:     `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>`,
  close:     `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>`
};

// ── Dashicons ──────────────────────────────────────────────
const DASHICONS = [
  'admin-appearance', 'admin-collapse', 'admin-comments', 'admin-customizer', 'admin-dashboard',
  'admin-generic', 'admin-home', 'admin-links', 'admin-media', 'admin-multisite', 'admin-network',
  'admin-page', 'admin-plugins', 'admin-post', 'admin-settings', 'admin-site', 'admin-site-alt',
  'admin-site-alt2', 'admin-site-alt3', 'admin-tools', 'admin-users', 'airplane', 'album', 'align-center',
  'align-full-width', 'align-left', 'align-none', 'align-pull-left', 'align-pull-right', 'align-right',
  'align-wide', 'amazon', 'analytics', 'archive', 'arrow-down', 'arrow-down-alt', 'arrow-down-alt2',
  'arrow-left', 'arrow-left-alt', 'arrow-left-alt2', 'arrow-right', 'arrow-right-alt', 'arrow-right-alt2',
  'arrow-up', 'arrow-up-alt', 'arrow-up-alt2', 'art', 'awards', 'backup', 'beer', 'bell', 'block-default',
  'book', 'book-alt', 'buddicons-activity', 'buddicons-bbpress-logo', 'buddicons-buddypress-logo',
  'buddicons-community', 'buddicons-forums', 'buddicons-friends', 'buddicons-groups', 'buddicons-pm',
  'buddicons-replies', 'buddicons-topics', 'buddicons-tracking', 'building', 'building-42', 'businessman',
  'businessperson', 'businesswoman', 'button', 'calculator', 'calendar', 'calendar-alt', 'camera', 'camera-alt',
  'car', 'category', 'chart-area', 'chart-bar', 'chart-line', 'chart-pie', 'clipboard', 'clock', 'cloud',
  'controls-back', 'controls-forward', 'controls-pause', 'controls-play', 'controls-repeat', 'controls-skipback',
  'controls-skipforward', 'controls-volumeoff', 'controls-volumeon', 'cover-image', 'dashboard', 'database',
  'database-add', 'database-export', 'database-import', 'database-remove', 'database-view', 'desktop',
  'dismiss', 'download', 'drumstick', 'edit', 'edit-large', 'edit-page', 'editor-aligncenter', 'editor-alignleft',
  'editor-alignright', 'editor-bold', 'editor-break', 'editor-code', 'editor-code-duplicate', 'editor-contract',
  'editor-customchar', 'editor-expand', 'editor-help', 'editor-indent', 'editor-insertmore', 'editor-italic',
  'editor-justify', 'editor-kitchensink', 'editor-ltr', 'editor-ol', 'editor-ol-rtl', 'editor-outdent',
  'editor-paragraph', 'editor-paste-text', 'editor-paste-word', 'editor-quote', 'editor-removeformatting',
  'editor-rtl', 'editor-spellcheck', 'editor-strikethrough', 'editor-table', 'editor-textcolor', 'editor-underline',
  'editor-unlink', 'editor-ul', 'email', 'email-alt', 'email-alt2', 'excerpt-view', 'external', 'facebook',
  'facebook-alt', 'feedback', 'filter', 'flag', 'food', 'format-aside', 'format-audio', 'format-chat', 'format-gallery',
  'format-image', 'format-quote', 'format-status', 'format-video', 'forms', 'fullscreen-alt', 'fullscreen-exit-alt',
  'games', 'google', 'grid-view', 'groups', 'hammer', 'heading', 'heart', 'hidden', 'hourglass', 'html', 'id',
  'id-alt', 'image-crop', 'image-filter', 'image-flip', 'image-rotate', 'image-rotate-left', 'image-rotate-right',
  'images-alt', 'images-alt2', 'index-card', 'info', 'info-outline', 'insert-after', 'insert-before', 'insert',
  'instagram', 'keyboard-hide', 'laptop', 'layout', 'leftright', 'lightbulb', 'list-view', 'location', 'location-alt',
  'lock', 'marker', 'media-archive', 'media-audio', 'media-code', 'media-default', 'media-document', 'media-interactive',
  'media-spreadsheet', 'media-text', 'media-video', 'megaphone', 'menu', 'menu-alt', 'menu-alt2', 'menu-alt3',
  'microphone', 'migrate', 'minus', 'money', 'move', 'nametag', 'networking', 'no', 'no-alt', 'palmtree', 'paperclip',
  'pdf', 'performance', 'pets', 'phone', 'pinterest', 'playlist-audio', 'playlist-video', 'plus', 'plus-alt',
  'plus-alt2', 'portfolio', 'post-status', 'pressthis', 'products', 'publish', 'randomize', 'redo', 'remove',
  'rest-api', 'rss', 'saved', 'schedule', 'screenoptions', 'search', 'share', 'share-alt', 'share-alt2', 'shield',
  'shield-alt', 'shortcode', 'slides', 'smartphone', 'smiley', 'sort', 'sos', 'spotify', 'star-empty', 'star-filled',
  'star-half', 'sticky', 'store', 'tablet', 'tag', 'tagcloud', 'testimonial', 'text', 'text-page', 'thumbs-down',
  'thumbs-up', 'tickets', 'tickets-alt', 'tide', 'translation', 'trash', 'twitch', 'twitter', 'undo', 'universal-access',
  'universal-access-alt', 'unlock', 'update', 'update-alt', 'upload', 'vault', 'video-alt', 'video-alt2', 'video-alt3',
  'visibility', 'warning', 'welcome-add-page', 'welcome-comments', 'welcome-learn-more', 'welcome-view-site',
  'welcome-widgets-menus', 'wordpress', 'wordpress-alt', 'yes', 'yes-alt'
];

// ── State ──────────────────────────────────────────────────
const urlParams = new URLSearchParams(window.location.search);
const initPageUrl = urlParams.get('page');

const state = {
  page:   initPageUrl === 'coderembassy-meta-settings' ? 'settings' : 'dashboard',
  theme:  'light',
  groups: cmfwAjax.groups || [],
  term_names: cmfwAjax.term_names || {},
  settings: cmfwAjax.settings || {
    enable_meta: '1',
    meta_position: 'woocommerce_product_additional_information',
    meta_heading: 'Product Information',
    heading_color: '#333333',
    heading_size: 18,
    meta_font_size: 14,
    meta_text_color: '#666666',
    meta_bg_color: '#ffffff'
  }
};

function escHtml(str) {
  return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function initials(name) {
  const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
  const a = parts[0]?.[0] || 'C';
  const b = parts[1]?.[0] || parts[0]?.[1] || 'E';
  return (a + b).toUpperCase();
}

// ── Theme ──────────────────────────────────────────────────
function loadTheme() {
  const saved = localStorage.getItem('cmfw_theme');
  state.theme = saved === 'dark' ? 'dark' : 'light';
  if (root.dataset) root.dataset.theme = state.theme;
}

function toggleTheme() {
  state.theme = state.theme === 'dark' ? 'light' : 'dark';
  if (root.dataset) root.dataset.theme = state.theme;
  localStorage.setItem('cmfw_theme', state.theme);
  render();
}

// ── Topbar ──────────────────────────────────────────────────
function renderTopbar() {
  const themeIcon = state.theme === 'dark' ? icons.sun : icons.moon;
  return `
    <div class="dpp-topbar">
      <div></div>
      <div class="dpp-top-actions">
        <button class="dpp-iconbtn" id="dpp-theme-toggle" title="Toggle theme" aria-label="Toggle theme">
          ${themeIcon}
        </button>
        <div class="dpp-userpill" title="${escHtml(CURRENT_USER.display_name)}">
          <div class="dpp-avatar">${initials(CURRENT_USER.display_name)}</div>
          <div class="name">${escHtml(CURRENT_USER.display_name)}</div>
        </div>
      </div>
    </div>
  `;
}

function bindTopbar() {
  document.getElementById('dpp-theme-toggle')?.addEventListener('click', toggleTheme);
}

// ── Sidebar ────────────────────────────────────────────────
function renderSidebar() {
  const nav = [
    { key: 'dashboard', icon: 'dashboard', label: 'Product Info' },
    { key: 'settings',  icon: 'settings',  label: 'Settings' },
  ];

  const logoUrl = cmfwAjax.logo_light && state.theme === 'dark' && cmfwAjax.logo_dark ? cmfwAjax.logo_dark : cmfwAjax.logo_light;
  return `
    <aside class="dpp-sidenav">
      <div class="dpp-brand">
        ${logoUrl
          ? `<img src="${escHtml(logoUrl)}" alt="CoderEmbassy Product Info" class="dpp-brand-logo" width="140" height="40">`
          : `<div class="dpp-brand-mark" aria-hidden="true"></div>
        <div>
          <div class="dpp-brand-title">Product Info</div>
        </div>`}
      </div>
      <nav class="dpp-nav" aria-label="Plugin navigation">
        ${nav.map(n => `
          <a class="${state.page === n.key ? 'active' : ''}" data-page="${n.key}">
            ${icons[n.icon]} <span>${n.label}</span>
          </a>
        `).join('')}
      </nav>
      <div class="dpp-sidenav-footer">
        <div>Theme: <strong>${state.theme === 'dark' ? 'Dark' : 'Light'}</strong></div>
      </div>
    </aside>
  `;
}

function bindNav() {
  document.querySelectorAll('.dpp-nav a[data-page]').forEach(el => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      updateStateFromDOM();
      state.page = el.dataset.page;
      render();
      
      // Update URL to match standard WP navigation but without reloading
      const url = new URL(window.location);
      url.searchParams.set('page', state.page === 'settings' ? 'coderembassy-meta-settings' : 'coderembassy-product-info-icons-images-text');
      window.history.pushState({}, '', url);
    });
  });
}

function updateStateFromDOM() {
  if (state.page === 'settings') {
    state.settings.enable_meta = document.getElementById('cmfw_enable_meta')?.checked ? '1' : '0';
    state.settings.show_heading = document.getElementById('cmfw_show_heading')?.checked ? '1' : '0';
    state.settings.meta_heading = document.getElementById('cmfw_meta_heading')?.value || 'Product Information';
    state.settings.meta_position = document.getElementById('cmfw_meta_position')?.value || 'woocommerce_after_add_to_cart_button';
    state.settings.heading_color = document.getElementById('cmfw_heading_color')?.value || '#333333';
    state.settings.heading_size = document.getElementById('cmfw_heading_size')?.value || 18;
    state.settings.meta_font_size = document.getElementById('cmfw_meta_font_size')?.value || 14;
    state.settings.meta_text_color = document.getElementById('cmfw_meta_text_color')?.value || '#666666';
    state.settings.meta_bg_color = document.getElementById('cmfw_meta_bg_color')?.value || '#ffffff';
    state.settings.image_width = document.getElementById('cmfw_image_width')?.value || 24;
    state.settings.image_height = document.getElementById('cmfw_image_height')?.value || 24;
  } else if (state.page === 'dashboard') {
    // groups are updated in realtime using events for inputs, but let's sweep just in case
    state.groups.forEach((g, gIdx) => {
      const taxEl = document.querySelector(`select[data-group="${gIdx}"]`);
      if (taxEl) g.taxonomy = taxEl.value;
      
      g.items.forEach((item, iIdx) => {
        const titleEl = document.querySelector(`input[data-group="${gIdx}"][data-item="${iIdx}"][data-field="title"]`);
        const subtitleEl = document.querySelector(`input[data-group="${gIdx}"][data-item="${iIdx}"][data-field="subtitle"]`);
        const iconEl = document.querySelector(`input[data-group="${gIdx}"][data-item="${iIdx}"][data-field="icon"]`);
        const imageEl = document.querySelector(`input[data-group="${gIdx}"][data-item="${iIdx}"][data-field="image_id"]`);
        
        if (titleEl) item.title = titleEl.value;
        if (subtitleEl) item.subtitle = subtitleEl.value;
        if (iconEl) item.icon = iconEl.value;
        if (imageEl) item.image_id = parseInt(imageEl.value, 10) || 0;
      });
    });
  }
}

// ── Main render ────────────────────────────────────────────
function render() {
  if (!root) return;
  root.innerHTML = `
    <div class="dpp-app">
      ${renderSidebar()}
      <section class="dpp-content">
        ${renderTopbar()}
        <main id="dpp-main">${renderPage()}</main>
      </section>
    </div>
  `;
  bindNav();
  bindTopbar();
  bindPageEvents();
}

function renderPage() {
  if (state.page === 'dashboard') return renderDashboard();
  if (state.page === 'settings')  return renderSettings();
  return renderDashboard();
}

// ── Dashboard / Groups ─────────────────────────────────────
function renderDashboard() {
  return `
    <div class="dpp-pagehead">
      <div>
        <div class="dpp-h1">Product Info Groups</div>
        <div class="dpp-sub">Manage product info rules, icons, and text</div>
      </div>
      <div>
         ${IS_PRO ? `<button class="dpp-btn dpp-btn-primary" id="cmfw-add-group">${icons.plus} Add Group</button>` : `<button class="dpp-btn dpp-btn-primary" disabled title="Upgrade to PRO to add more groups">${icons.plus} Add Group (PRO)</button>`}
      </div>
    </div>
    <div id="cmfw-groups-container">
      ${state.groups.length === 0 ? '<div class="dpp-card">No groups configured</div>' : state.groups.map((group, index) => renderGroup(group, index)).join('')}
    </div>
    <div id="dpp-settings-msg" class="dpp-settings-saved-msg" style="display:none"></div>
    <div style="margin-top:20px;display:flex;justify-content:flex-end;">
      <button class="dpp-btn dpp-btn-primary" id="cmfw-save-data">Save Data</button>
    </div>
  `;
}

function renderGroup(group, index) {
  const terms = group.terms || [];
  
  return `
    <div class="dpp-card" style="margin-bottom: 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
        <div style="font-weight:900; font-size:16px;">Product Info Group ${index + 1}</div>
        ${index > 0 && IS_PRO ? `<button class="dpp-btn dpp-btn-danger dpp-btn-sm cmfw-remove-group" data-index="${index}">${icons.trash} Remove</button>` : ''}
      </div>
      
      <div class="dpp-form-grid" style="margin-bottom: 24px;">
        <div class="dpp-form-group full">
          <label class="dpp-label">Select Taxonomy Type</label>
          <select class="dpp-select" data-group="${index}" data-field="taxonomy">
            <option value="">Select taxonomy</option>
            <option value="product_cat" ${group.taxonomy === 'product_cat' ? 'selected' : ''}>Category</option>
            <option value="product_tag" ${group.taxonomy === 'product_tag' ? 'selected' : ''}>Tag</option>
          </select>
        </div>
        
        ${group.taxonomy ? `
        <div class="dpp-form-group full">
          <label class="dpp-label">Select category/tags</label>
          <div style="padding:10px; border:1px solid var(--border); background:var(--card-2); border-radius:8px; display:flex; gap:8px; flex-wrap:wrap; min-height:45px;">
             ${terms.map(tid => `<span class="dpp-userpill" style="font-size:12px; padding:4px 8px;">${escHtml(state.term_names[tid] || 'Term #'+tid)} <button class="cmfw-remove-term" data-group="${index}" data-term="${tid}" style="background:none;border:none;cursor:pointer;color:var(--danger);font-weight:bold;margin-left:4px;">&times;</button></span>`).join('')}
             ${terms.length === 0 ? '<span style="color:var(--muted);font-size:13px;padding:4px">No terms selected. Type below to search.</span>' : ''}
          </div>
          <div style="position:relative; margin-top:8px;">
            <input type="text" class="dpp-input cmfw-term-search" data-group="${index}" placeholder="Search terms (requires taxonomy)...">
            <div class="dpp-ac-list" id="cmfw-term-list-${index}" style="display:none; position:absolute; width:100%; top:100%; left:0; z-index:10; border:1px solid var(--border); background:var(--card); max-height:200px; overflow-y:auto;"></div>
          </div>
        </div>
        ` : ''}
      </div>

      <div style="font-weight:800; margin-bottom: 12px; color:var(--text); padding-bottom:8px; border-bottom:1px solid var(--border)">Product Info Items</div>
      
      <div class="cmfw-items-grid">
        ${group.items.map((item, iIdx) => renderItem(item, index, iIdx)).join('')}
      </div>
      
      ${IS_PRO ? `
      <div style="margin-top:16px;">
         <button class="dpp-btn dpp-btn-sm cmfw-add-item" data-group="${index}">${icons.plus} Add Item</button>
      </div>
      ` : ''}
    </div>
  `;
}

// renderItem function here

function renderItem(item, gIdx, iIdx) {
  return `
    <div class="cmfw-item-card">
      ${IS_PRO || iIdx >= 3 ? `<button class="cmfw-remove-item cmfw-item-close" data-group="${gIdx}" data-item="${iIdx}" title="Remove Item">${icons.close}</button>` : ''}
      
      <div style="font-weight:700; margin-bottom:12px;">Item ${iIdx + 1}</div>
      <div class="dpp-form-group full" style="margin-bottom:12px;">
         <label class="dpp-label">Product Info Text</label>
         <input type="text" class="dpp-input" data-group="${gIdx}" data-item="${iIdx}" data-field="title" value="${escHtml(item.title)}">
      </div>
      
      <div class="dpp-form-group full" style="margin-bottom:12px;">
         <label class="dpp-label">Product Info Subtitle ${!IS_PRO ? '<span style="color:var(--primary);font-size:10px;margin-left:5px;background:rgba(99,102,241,0.1);padding:2px 6px;border-radius:4px;">PRO</span>' : ''}</label>
         <input type="text" class="dpp-input" data-group="${gIdx}" data-item="${iIdx}" data-field="subtitle" value="${!IS_PRO ? '' : escHtml(item.subtitle || '')}" ${!IS_PRO ? 'disabled placeholder="Upgrade to PRO for subtitles"' : ''}>
      </div>
      
      <div style="display:flex; gap:24px; flex-wrap:wrap;">
         <div class="dpp-form-group">
            <label class="dpp-label">Dashicon Icon</label>
            <div style="display:flex; align-items:center; gap:10px;">
               <input type="hidden" data-group="${gIdx}" data-item="${iIdx}" data-field="icon" value="${escHtml(item.icon)}">
               ${item.icon ? `<span class="dashicons dashicons-${escHtml(item.icon)}" style="font-size:32px;width:32px;height:32px;margin-top:2px;color:var(--text)"></span>` : ''}
               <button class="dpp-btn dpp-btn-sm cmfw-open-icon-picker" data-group="${gIdx}" data-item="${iIdx}">Select Dashicon</button>
               ${item.icon ? `<button class="dpp-btn dpp-btn-sm dpp-btn-danger cmfw-remove-icon" data-group="${gIdx}" data-item="${iIdx}">Remove</button>` : ''}
            </div>
         </div>
         <div class="dpp-form-group">
            <label class="dpp-label">Image Upload</label>
            <div style="display:flex; align-items:center; gap:10px;">
               <input type="hidden" data-group="${gIdx}" data-item="${iIdx}" data-field="image_id" value="${item.image_id}">
               ${item.image_url ? `<img src="${escHtml(item.image_url)}" style="width:40px;height:40px;border-radius:4px;object-fit:cover;border:1px solid var(--border)">` : ''}
               <button class="dpp-btn dpp-btn-sm cmfw-upload-image" data-group="${gIdx}" data-item="${iIdx}">${icons.image} ${item.image_id ? 'Change' : 'Select'} Image</button>
               ${item.image_id ? `<button class="dpp-btn dpp-btn-sm dpp-btn-danger cmfw-remove-image" data-group="${gIdx}" data-item="${iIdx}">Remove</button>` : ''}
            </div>
         </div>
      </div>
    </div>
  `;
}

// ── Settings ───────────────────────────────────────────────
function renderSettings() {
  const s = state.settings;
  return `
    <div class="dpp-pagehead">
      <div>
        <div class="dpp-h1">Settings</div>
        <div class="dpp-sub">Configure display options for Product Info</div>
      </div>
    </div>
    
    <div class="dpp-card" style="margin-bottom:20px;">
       <div class="dpp-form-grid">
         <div class="dpp-form-group full">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;color:var(--text);font-weight:700">
              <input type="checkbox" id="cmfw_enable_meta" value="1" ${s.enable_meta === '1' ? 'checked' : ''}>
              <span>Enable Product Info display on product pages</span>
            </label>
         </div>
         <div class="dpp-form-group full" style="margin-top:10px;">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;color:var(--text);font-weight:700">
              <input type="checkbox" id="cmfw_show_heading" value="1" ${s.show_heading === '1' ? 'checked' : ''}>
              <span>Show Product Info Heading</span>
            </label>
         </div>
         <div class="dpp-form-group full" style="margin-top:10px;">
            <label class="dpp-label">Product Info Heading</label>
            <input type="text" class="dpp-input" id="cmfw_meta_heading" value="${escHtml(s.meta_heading)}">
         </div>
         <div class="dpp-form-group full" style="margin-top:20px; border-top: 1px solid var(--border); padding-top: 20px;">
            <label class="dpp-label">Display Position ${!IS_PRO ? '<span style="color:var(--primary);font-size:10px;margin-left:5px;background:rgba(var(--primary-rgb),0.1);padding:2px 6px;border-radius:4px;">PRO</span>' : ''}</label>
            <select class="dpp-select" id="cmfw_meta_position" ${!IS_PRO ? 'disabled' : ''}>
              <option value="woocommerce_after_add_to_cart_button" ${s.meta_position === 'woocommerce_after_add_to_cart_button' ? 'selected' : ''}>After Add to Cart Button (Default)</option>
              ${Object.entries(cmfwAjax.allowed_positions || {}).map(([val, label]) => {
                if (val === 'woocommerce_after_add_to_cart_button') return '';
                return `<option value="${val}" ${s.meta_position === val ? 'selected' : ''}>${escHtml(label)}</option>`;
              }).join('')}
            </select>
            ${!IS_PRO ? '<div style="font-size:11px;color:var(--muted);margin-top:5px;">Free version is restricted to "After Add to Cart Button" position.</div>' : ''}
         </div>
       </div>
    </div>

    <div class="dpp-card">
       <div style="font-weight:800; margin-bottom: 16px;">Design Settings</div>
       <div class="dpp-form-grid">
         
        <div class="dpp-form-group">
          <label class="dpp-label" for="cmfw_heading_color">Heading Font Color</label>
          <div style="display:flex; gap:10px;">
            <input type="color" value="${escHtml(s.heading_color)}" style="width: 40px; height: 40px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; padding: 2px;" onchange="document.getElementById('cmfw_heading_color').value = this.value">
            <input type="text" class="dpp-input" id="cmfw_heading_color" value="${escHtml(s.heading_color)}">
          </div>
        </div>

        <div class="dpp-form-group">
          <label class="dpp-label" for="cmfw_heading_size">Heading Font Size (px)</label>
          <input type="number" class="dpp-input" id="cmfw_heading_size" value="${s.heading_size}">
        </div>

        <div class="dpp-form-group">
          <label class="dpp-label" for="cmfw_meta_text_color">Text Color</label>
          <div style="display:flex; gap:10px;">
            <input type="color" value="${escHtml(s.meta_text_color)}" style="width: 40px; height: 40px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; padding: 2px;" onchange="document.getElementById('cmfw_meta_text_color').value = this.value">
            <input type="text" class="dpp-input" id="cmfw_meta_text_color" value="${escHtml(s.meta_text_color)}">
          </div>
        </div>

        <div class="dpp-form-group">
          <label class="dpp-label" for="cmfw_meta_font_size">Text Font Size (px)</label>
          <input type="number" class="dpp-input" id="cmfw_meta_font_size" value="${s.meta_font_size}">
        </div>

        <div class="dpp-form-group">
          <label class="dpp-label" for="cmfw_meta_bg_color">Background Color</label>
          <div style="display:flex; gap:10px;">
            <input type="color" value="${escHtml(s.meta_bg_color)}" style="width: 40px; height: 40px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; padding: 2px;" onchange="document.getElementById('cmfw_meta_bg_color').value = this.value">
            <input type="text" class="dpp-input" id="cmfw_meta_bg_color" value="${escHtml(s.meta_bg_color)}">
          </div>
        </div>

        <div class="dpp-form-group">
          <label class="dpp-label" for="cmfw_image_width">Image Width (px)</label>
          <input type="number" class="dpp-input" id="cmfw_image_width" value="${s.image_width || 24}">
        </div>

        <div class="dpp-form-group">
          <label class="dpp-label" for="cmfw_image_height">Image Height (px)</label>
          <input type="number" class="dpp-input" id="cmfw_image_height" value="${s.image_height || 24}">
        </div>

       </div>
       <div id="dpp-settings-msg" class="dpp-settings-saved-msg" style="display:none;margin-top:16px"></div>
       <div style="margin-top:20px;display:flex;justify-content:flex-end;">
         <button class="dpp-btn dpp-btn-primary" id="cmfw-save-data">Save Settings</button>
       </div>
    </div>
  `;
}

// ── Bind Events ────────────────────────────────────────────
function bindPageEvents() {
  if (state.page === 'dashboard') {
    document.querySelectorAll('select[data-field="taxonomy"]').forEach(el => {
      el.addEventListener('change', e => {
        const gIdx = e.target.dataset.group;
        state.groups[gIdx].taxonomy = e.target.value;
        state.groups[gIdx].terms = []; // reset terms when taxonomy changes
        render();
      });
    });

    document.querySelectorAll('.cmfw-term-search').forEach(input => {
      const gIdx = input.dataset.group;
      const list = document.getElementById(`cmfw-term-list-${gIdx}`);
      
      const debouncedSearch = debounce(async (q) => {
        const tax = state.groups[gIdx]?.taxonomy;
        if (!q || !tax || !list) { 
          if (list) list.style.display = 'none'; 
          return; 
        }
        
        const fd = new URLSearchParams();
        fd.append('action', 'cmfw_search_terms');
        fd.append('nonce', NONCE);
        fd.append('q', q);
        fd.append('taxonomy', tax);
        
        try {
          const res = await fetch(AJAX_URL, { method: 'POST', body: fd });
          const json = await res.json();
          if (json.success && json.data.length) {
            list.innerHTML = json.data.map(t => `<div class="dpp-ac-item" data-id="${t.id}" data-name="${escHtml(t.name)}" style="padding:10px;cursor:pointer;border-bottom:1px solid var(--border);color:var(--text)">${escHtml(t.name)}</div>`).join('');
            list.style.display = 'block';
            
            list.querySelectorAll('.dpp-ac-item').forEach(li => {
              li.addEventListener('click', () => {
                const termId = parseInt(li.dataset.id, 10);
                if (!state.groups[gIdx].terms.includes(termId)) {
                  state.groups[gIdx].terms.push(termId);
                  state.term_names[termId] = li.dataset.name;
                }
                list.style.display = 'none';
                render();
              });
            });
          } else {
            list.style.display = 'none';
          }
        } catch (err) {
            if (list) list.style.display = 'none';
        }
      }, 300);

      input.addEventListener('input', e => {
        debouncedSearch(e.target.value.trim());
      });
    });

    document.querySelectorAll('.cmfw-remove-term').forEach(btn => {
      btn.addEventListener('click', e => {
        const gIdx = e.currentTarget.dataset.group;
        const termId = parseInt(e.currentTarget.dataset.term, 10);
        state.groups[gIdx].terms = state.groups[gIdx].terms.filter(id => id !== termId);
        render();
      });
    });

    document.querySelectorAll('.cmfw-upload-image').forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        const gIdx = e.currentTarget.dataset.group;
        const iIdx = e.currentTarget.dataset.item;
        
        if (typeof wp !== 'undefined' && wp.media) {
           const mediaUploader = wp.media({
             title: cmfwAjax.media_title || 'Select Image',
             button: { text: cmfwAjax.media_button || 'Use Image' },
             multiple: false
           });
           mediaUploader.on('select', function() {
              const attachment = mediaUploader.state().get('selection').first().toJSON();
              updateStateFromDOM(); // keep other inputs 
              state.groups[gIdx].items[iIdx].image_id = attachment.id;
              state.groups[gIdx].items[iIdx].image_url = attachment.url;
              state.groups[gIdx].items[iIdx].icon = ''; // clear icon
              render();
           });
           mediaUploader.open();
        } else {
           toast('Media uploader not available', 'error');
        }
      });
    });

    document.querySelectorAll('.cmfw-remove-image').forEach(btn => {
      btn.addEventListener('click', e => {
        updateStateFromDOM();
        const gIdx = e.currentTarget.dataset.group;
        const iIdx = e.currentTarget.dataset.item;
        state.groups[gIdx].items[iIdx].image_id = 0;
        state.groups[gIdx].items[iIdx].image_url = '';
        render();
      });
    });
    
    document.querySelectorAll('.cmfw-open-icon-picker').forEach(btn => {
       btn.addEventListener('click', e => {
          e.preventDefault();
          const gIdx = e.currentTarget.dataset.group;
          const iIdx = e.currentTarget.dataset.item;
          openDashiconModal(gIdx, iIdx);
       });
    });

    document.querySelectorAll('.cmfw-remove-icon').forEach(btn => {
       btn.addEventListener('click', e => {
          e.preventDefault();
          updateStateFromDOM();
          const gIdx = e.currentTarget.dataset.group;
          const iIdx = e.currentTarget.dataset.item;
          state.groups[gIdx].items[iIdx].icon = '';
          render();
       });
    });

    document.getElementById('cmfw-add-group')?.addEventListener('click', () => {
       updateStateFromDOM();
       state.groups.push({
          taxonomy: '', terms: [], items: [
             {title:'', subtitle:'', icon:'', image_id:0}, {title:'', subtitle:'', icon:'', image_id:0}, {title:'', subtitle:'', icon:'', image_id:0}
          ]
       });
       render();
       toast('Group added');
    });

    document.querySelectorAll('.cmfw-remove-group').forEach(btn => {
       btn.addEventListener('click', e => {
          if (!confirm('Are you sure you want to remove this group?')) return;
          updateStateFromDOM();
          const gIdx = e.currentTarget.dataset.index;
          state.groups.splice(gIdx, 1);
          render();
          toast('Group removed');
       });
    });
    
    document.querySelectorAll('.cmfw-add-item').forEach(btn => {
      btn.addEventListener('click', e => {
         updateStateFromDOM();
         const gIdx = e.currentTarget.dataset.group;
         state.groups[gIdx].items.push({title:'', subtitle:'', icon:'', image_id:0});
         render();
      });
    });
    
    document.querySelectorAll('.cmfw-remove-item').forEach(btn => {
       btn.addEventListener('click', e => {
          if (!confirm('Remove this item?')) return;
          updateStateFromDOM();
          const gIdx = e.currentTarget.dataset.group;
          const iIdx = e.currentTarget.dataset.item;
          state.groups[gIdx].items.splice(iIdx, 1);
          render();
       });
    });
  }
}

// ── Modals ─────────────────────────────────────────────────
function openDashiconModal(gIdx, iIdx) {
  const overlay = document.createElement('div');
  overlay.className = 'dpp-overlay';
  overlay.innerHTML = `
    <div class="dpp-modal" style="max-width: 680px; width: 100%;">
      <div class="dpp-modal-header">
        <div class="dpp-modal-title">Select Dashicon</div>
        <button class="dpp-modal-close" id="dpp-icon-close">&times;</button>
      </div>
      <div class="dpp-modal-body">
        <input type="text" class="dpp-input" id="dpp-icon-search" placeholder="Search icons..." style="margin-bottom: 20px; font-size: 15px;">
        <div id="dpp-icon-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 12px; max-height: 480px; overflow-y: auto; padding: 12px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--card-2);">
          ${DASHICONS.map(icon => `
            <div class="dpp-icon-item" data-icon="${icon}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px 8px; cursor: pointer; border-radius: 10px; border: 1px solid transparent; background: var(--card); transition: all 0.2s;">
              <span class="dashicons dashicons-${icon}" style="font-size: 28px; width: 28px; height: 28px; color: var(--text);"></span>
              <span style="font-size: 11px; margin-top: 10px; text-align: center; color: var(--muted); word-break: break-all; line-height: 1.2;">${icon}</span>
            </div>
          `).join('')}
        </div>
      </div>
    </div>
  `;
  root.appendChild(overlay);

  const style = document.createElement('style');
  style.id = 'dpp-icon-style';
  style.innerHTML = `
    .dpp-icon-item:hover { border-color: var(--primary) !important; box-shadow: var(--shadow-sm); transform: translateY(-2px); }
  `;
  document.head.appendChild(style);

  function closeModal() {
    overlay.remove();
    style.remove();
  }

  overlay.querySelector('#dpp-icon-close').addEventListener('click', closeModal);
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) closeModal();
  });

  const searchInput = overlay.querySelector('#dpp-icon-search');
  searchInput.addEventListener('input', (e) => {
    const term = e.target.value.toLowerCase();
    overlay.querySelectorAll('.dpp-icon-item').forEach(el => {
      const iconName = el.dataset.icon;
      if (iconName.includes(term)) {
        el.style.display = 'flex';
      } else {
        el.style.display = 'none';
      }
    });
  });

  overlay.querySelectorAll('.dpp-icon-item').forEach(el => {
    el.addEventListener('click', () => {
      updateStateFromDOM();
      state.groups[gIdx].items[iIdx].icon = el.dataset.icon;
      state.groups[gIdx].items[iIdx].image_id = 0; // Exclusivity
      state.groups[gIdx].items[iIdx].image_url = '';
      closeModal();
      render();
    });
  });
  
  setTimeout(() => searchInput.focus(), 100);
}

// ── Save Data ──────────────────────────────────────────────
async function saveData() {
  updateStateFromDOM();
  const btn = document.getElementById('cmfw-save-data');
  const msgEl = document.getElementById('dpp-settings-msg');
  if (btn) btn.disabled = true;
  if (msgEl) {
    msgEl.style.display = 'block';
    msgEl.textContent = 'Saving...';
    msgEl.classList.remove('error');
  }

  try {
    const formData = new URLSearchParams();
    formData.append('action', 'cmfw_save_spa_data');
    formData.append('nonce', NONCE);
    
    if (state.page === 'dashboard') {
       formData.append('groups', JSON.stringify(state.groups));
    } else {
       formData.append('settings', JSON.stringify(state.settings));
    }

    const res = await fetch(AJAX_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString()
    });
    
    const text = await res.text();
    let data;
    try { data = JSON.parse(text); } catch (e) { throw new Error(text); }
    
    if (!data.success) {
      throw new Error(data.data || 'Unknown error');
    }

    if (msgEl) {
      msgEl.textContent = 'Saved successfully.';
      msgEl.classList.remove('error');
      setTimeout(() => msgEl.style.display = 'none', 3000);
    }
    toast('Data saved');
  } catch (e) {
    if (msgEl) {
      msgEl.textContent = 'Save failed: ' + e.message;
      msgEl.classList.add('error');
    }
    toast('Save failed', 'error');
  } finally {
    if (btn) btn.disabled = false;
  }
}

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => { clearTimeout(timeout); func(...args); };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// ── Init ────────────────────────────────────────────────────
loadTheme();
render();

// Global click handler for saving
root.addEventListener('click', function(e) {
  if (e.target.id === 'cmfw-save-data' || e.target.closest('#cmfw-save-data')) {
    e.preventDefault();
    saveData();
  }
});

})();
