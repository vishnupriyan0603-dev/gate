import { api, qs, el } from './api';
import { toast } from './ui';
import { pop } from './fx';

export function init(): void {
  initSync();
  initSettingsForm();
  initChecklists();
  initAiSettings();
  initTourReplay();
}

function initTourReplay(): void {
  qs<HTMLButtonElement>('#tour-replay')?.addEventListener('click', (e) => {
    pop(e.currentTarget as HTMLElement);
    void import('./tour').then((m) => m.startTour('settings'));
  });
}

function initAiSettings(): void {
  const status = qs<HTMLElement>('#ai-status');
  const testBtn = qs<HTMLButtonElement>('#btn-groq-test');
  if (!status) return;

  api<{ configured: boolean; model: string }>('/api/ai/status').then((s) => {
    status.textContent = s.configured
      ? `✅ Groq configured · model ${s.model}`
      : '⚠ No Groq key yet — AI MCQs and the extension will not work until you add one.';
  });

  const form = qs<HTMLFormElement>('#ai-settings-form');
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const get = (id: string) => qs<HTMLInputElement>(id)!.value.trim();
    try {
      await api('/api/settings/save', {
        method: 'POST',
        body: JSON.stringify({ groq_api_key: get('#set-groq-key'), groq_model: get('#set-groq-model') }),
      });
      toast('AI settings saved', 'success');
      status.textContent = '✅ Groq saved — run "Test key" to verify.';
    } catch (err) {
      toast(String(err), 'error');
    }
  });

  testBtn?.addEventListener('click', async () => {
    testBtn.disabled = true;
    testBtn.classList.add('sparkle');
    testBtn.textContent = 'Testing…';
    status.textContent = '';
    try {
      const r = await api<{ explanation: string }>('/api/ai/explain', {
        method: 'POST',
        body: JSON.stringify({ text: 'What is the time complexity of binary search? Answer briefly.' }),
      });
      status.textContent = '✅ Key works. Sample: ' + r.explanation.slice(0, 180) + '…';
      toast('Groq connection verified', 'success');
      pop(status);
    } catch (err) {
      status.textContent = '❌ ' + String(err);
      toast('Groq test failed', 'error');
    } finally {
      testBtn.disabled = false;
      testBtn.classList.remove('sparkle');
      testBtn.textContent = '⚡ Test key';
    }
  });
}

function initSync(): void {
  const btn = qs<HTMLButtonElement>('#btn-notion-sync');
  const status = qs<HTMLElement>('#sync-status');
  if (!btn || !status) return;

  api<{ configured: boolean; synced_at: string | null }>('/api/notion/status').then((s) => {
    status.textContent = s.configured
      ? `✅ Token configured · last sync ${s.synced_at || 'never'}`
      : '⚠ Notion token not configured';
  });

  btn.addEventListener('click', async () => {
    btn.disabled = true;
    btn.classList.add('sparkle');
    btn.textContent = 'Syncing…';
    status.textContent = '';
    try {
      const r = await api<{ message: string; report: Record<string, any> }>('/api/notion/sync', { method: 'POST', body: '{}' });
      const t = r.report.targets;
      status.textContent = `${r.message} · subjects:${r.report.subjects} phases:${r.report.phases} daily:${r.report.daily} checklists:${r.report.checklists} targets:${t?.rows ?? '—'}`;
      toast('Notion synced', 'success');
      setTimeout(() => location.reload(), 1200);
    } catch (e) {
      status.textContent = 'Syncing failed: ' + String(e);
      toast('Sync failed: ' + String(e), 'error');
    } finally {
      btn.disabled = false;
      btn.classList.remove('sparkle');
      btn.textContent = '🔄 Sync Notion now';
    }
  });
}

function initSettingsForm(): void {
  const form = qs<HTMLFormElement>('#settings-form');
  if (!form) return;
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const val = (id: string) => qs<HTMLInputElement>(id)!.value.trim();
    try {
      await api('/api/settings/save', {
        method: 'POST',
        body: JSON.stringify({
          notion_token: val('#set-token'),
          notion_plan_page: val('#set-plan'),
          notion_calendar_page: val('#set-cal'),
          exam_date: val('#set-exam'),
          target_score: val('#set-target'),
          weekly_hour_goal: val('#set-hours'),
        }),
      });
      toast('Settings saved', 'success');
    } catch (err) {
      toast(String(err), 'error');
    }
  });
}

function initChecklists(): void {
  const host = qs<HTMLElement>('#checklists');
  if (!host) return;
  api<{ groups: Record<string, any[]> }>('/api/checklists').then((d) => {
    host.innerHTML = '';
    let n = 0;
    for (const [group, items] of Object.entries(d.groups)) {
      const section = el('div', {});
      section.append(el('p', { class: 'mt-3 text-xs font-semibold uppercase tracking-wider text-indigo-300' }, [group]));
      for (const it of items) {
        if (!it.notion_block_id) continue;
        const row = el('label', { class: 'anim-fade-up mt-1 flex items-start gap-2 rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm cursor-pointer hover:border-violet-500/40 transition-all' });
        row.style.setProperty('--d', `${Math.min(n++, 14) * 35}ms`);
        const cb = el('input', { type: 'checkbox', class: 'checkbox checkbox-sm checkbox-primary mt-0.5' });
        cb.checked = !!it.checked;
        row.append(cb, el('span', { class: 'text-slate-300' }, [it.title]));
        cb.addEventListener('change', () => {
          api(`/api/checklists/${it.id}/toggle`, { method: 'POST', body: '{}' })
            .then(() => {
              it.checked = cb.checked ? 1 : 0;
              toast(it.checked ? 'Checked → synced to Notion' : 'Unchecked → synced to Notion', 'success');
            })
            .catch(() => {
              cb.checked = !!it.checked;
              toast('Sync failed', 'error');
            });
        });
        section.append(row);
      }
      if (items.length) host.append(section);
    }
  });
}