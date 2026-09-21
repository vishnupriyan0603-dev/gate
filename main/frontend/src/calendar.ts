import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import { api, fmtDMY, qs, qsa, todayStrIST } from './api';
import { toast } from './ui';
import { missionCheer } from './motivation';
import { pop } from './fx';

const subjectColors: Record<string, string> = {
  'DM': '#8b5cf6',
  'C': '#0ea5e9',
  'DS': '#06b6d4',
  'Algo': '#6366f1',
  'DBMS': '#10b981',
  'OS': '#f59e0b',
  'COA': '#f97316',
  'CN': '#ef4444',
  'TOC': '#ec4899',
  'Compiler': '#d946ef',
  'Engg Math': '#14b8a6',
  'Aptitude': '#84cc16',
  'Revision / Mock': '#e11d48',
  'Core': '#64748b',
};

export function init(): void {
  const host = qs<HTMLElement>('#calendar');
  if (!host) return;

  const loadCalendar = () => {
    api<any[]>('/api/calendar').then((rows) => {
      const byDate = new Map<string, any>();
      for (const r of rows) byDate.set(r.task_date, r);

      const front = qs<HTMLElement>('#day-front');
      const detail = qs<HTMLElement>('#day-detail');
      const dayDate = qs<HTMLElement>('#day-detail-date');
      const dayTask = qs<HTMLElement>('#day-detail-task');
      const dayStatus = qs<HTMLElement>('#day-detail-status');
      const dayNote = qs<HTMLElement>('#day-detail-note');
      const dayPyqs = qs<HTMLElement>('#day-detail-pyqs');
      const dayNotionLink = qs<HTMLAnchorElement>('#day-detail-notion');
      const dayToggleBtn = qs<HTMLButtonElement>('#day-detail-toggle');

      let currentSelectedDate = '';

      const swap = (showDetail: boolean) => {
        front?.classList.toggle('hidden', showDetail);
        if (!detail) return;
        detail.classList.toggle('hidden', !showDetail);
        if (showDetail) {
          detail.classList.remove('anim-fade-up');
          void detail.offsetWidth;
          detail.classList.add('anim-fade-up');
        }
      };

      qs<HTMLButtonElement>('#day-detail-back')?.addEventListener('click', (e) => {
        e.stopPropagation();
        swap(false);
      });

      const showDay = (date: string) => {
        currentSelectedDate = date;
        const r = byDate.get(date);
        if (dayDate) {
          const dObj = new Date(date + 'T00:00:00');
          const dayName = r?.weekday || dObj.toLocaleDateString('en-IN', { weekday: 'long', timeZone: 'Asia/Kolkata' });
          dayDate.textContent = `${dayName} · ${fmtDMY(date)}`;
        }
        if (dayTask) {
          dayTask.textContent = r ? r.task : 'Rest day — drill something small.';
        }
        if (dayStatus) {
          const isMock = r?.task?.toLowerCase().includes('6h:') || r?.task?.toLowerCase().includes('mock');
          dayStatus.innerHTML = r
            ? `<span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold ${r.status === 'done' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-800 text-slate-300 border border-slate-700'}">${r.status.toUpperCase()}</span> ${isMock ? '<span class="inline-block ml-1.5 px-2 py-0.5 rounded text-[11px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">🔥 6H MOCK DRILL</span>' : ''}`
            : 'No session scheduled.';
        }
        if (dayPyqs) {
          if (r?.pyqs) {
            dayPyqs.textContent = `🎯 Target: +${r.pyqs} PYQs`;
            dayPyqs.classList.remove('hidden');
          } else {
            dayPyqs.classList.add('hidden');
          }
        }
        if (dayNote) {
          if (r?.note) {
            dayNote.textContent = `📝 ${r.note}`;
            dayNote.classList.remove('hidden');
          } else {
            dayNote.classList.add('hidden');
          }
        }
        if (dayNotionLink) {
          if (r?.notion_url) {
            dayNotionLink.href = r.notion_url;
            dayNotionLink.classList.remove('hidden');
          } else {
            dayNotionLink.classList.add('hidden');
          }
        }
        if (dayToggleBtn) {
          if (r) {
            dayToggleBtn.classList.remove('hidden');
            dayToggleBtn.textContent = r.status === 'done' ? 'Mark Incomplete ↺' : 'Mark Complete ✓ (+20 XP)';
            dayToggleBtn.className = `btn btn-xs mt-3 w-full rounded-lg font-semibold transition-all ${r.status === 'done' ? 'border border-slate-700 bg-slate-800 text-slate-300 hover:bg-slate-700' : 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-950/40'}`;
          } else {
            dayToggleBtn.classList.add('hidden');
          }
        }

        const panel = qs<HTMLElement>('#day-panel');
        panel?.classList.remove('anim-fade-up');
        void panel?.offsetWidth;
        panel?.classList.add('anim-fade-up');
        swap(true);
      };

      dayToggleBtn?.addEventListener('click', async (e) => {
        e.stopPropagation();
        if (!currentSelectedDate) return;
        const r = byDate.get(currentSelectedDate);
        if (!r) return;
        const prevStatus = r.status;
        const nextStatus = r.status === 'done' ? 'pending' : 'done';
        r.status = nextStatus;
        showDay(currentSelectedDate);

        // Update calendar event visually
        const ev = cal.getEventById(currentSelectedDate);
        if (ev) {
          const color = nextStatus === 'done' ? '#10b981' : (subjectColors[r.subject] || '#7c3aed');
          ev.setExtendedProp('status', nextStatus);
          ev.setProp('backgroundColor', color);
          ev.setProp('borderColor', color);
        }

        try {
          await api(`/api/calendar/${currentSelectedDate}/status`, {
            method: 'POST',
            body: JSON.stringify({ status: nextStatus }),
          });
          if (nextStatus === 'done') {
            missionCheer('Day complete · +20 XP (Synced with Notion)');
          } else {
            toast('Day marked as pending (Synced with Notion)', 'info');
          }
        } catch (err) {
          // Roll back the optimistic update so UI matches the server.
          r.status = prevStatus;
          showDay(currentSelectedDate);
          const ev2 = cal.getEventById(currentSelectedDate);
          if (ev2) {
            const color = prevStatus === 'done' ? '#10b981' : (subjectColors[r.subject] || '#7c3aed');
            ev2.setExtendedProp('status', prevStatus);
            ev2.setProp('backgroundColor', color);
            ev2.setProp('borderColor', color);
          }
          toast(String(err), 'error');
        }
      });

      const events = rows.map((r) => {
        let color = subjectColors[r.subject] || '#7c3aed';
        if (r.status === 'done') color = '#10b981';
        else if (r.status === 'skipped') color = '#fb4d6d';

        const taskText = String(r.task ?? '');
        const isMock = taskText.toLowerCase().includes('6h:') || taskText.toLowerCase().includes('mock');
        const badge = r.pyqs ? ` [+${r.pyqs}]` : '';

        return {
          id: r.task_date,
          title: `${r.subject ? `[${r.subject}] ` : ''}${taskText}${badge}`,
          start: r.task_date,
          backgroundColor: color,
          borderColor: isMock ? '#f43f5e' : color,
          textColor: '#ffffff',
          extendedProps: {
            status: r.status,
            subject: r.subject,
            pyqs: r.pyqs,
            note: r.note,
            notion_url: r.notion_url,
          },
        };
      });

      const cal = new Calendar(host, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        initialDate: todayStrIST(),
        timeZone: 'Asia/Kolkata',
        locale: 'en-IN',
        height: 'auto',
        themeSystem: 'standard',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth',
        },
        dayMaxEvents: 2,
        events,
        dateClick: (info) => {
          pop(info.dayEl);
          showDay(info.dateStr.slice(0, 10));
        },
        eventClick: (info) => {
          pop(info.el);
          const date = info.event.id || info.event.startStr?.slice(0, 10);
          showDay(date);
        },
      });

      cal.render();

      // Month Jump Tabs
      qsa<HTMLButtonElement>('[data-month-jump]').forEach((btn) => {
        btn.addEventListener('click', () => {
          const target = btn.dataset.monthJump;
          if (target) {
            cal.gotoDate(target);
            qsa('[data-month-jump]').forEach((b) => b.classList.remove('bg-indigo-600', 'text-white'));
            btn.classList.add('bg-indigo-600', 'text-white');
          }
        });
      });

      // Notion Sync Button
      const syncBtn = qs<HTMLButtonElement>('#btn-notion-sync-cal');
      if (syncBtn) {
        syncBtn.addEventListener('click', async () => {
          syncBtn.disabled = true;
          syncBtn.innerHTML = `<span class="animate-spin inline-block mr-1">🔄</span> Syncing...`;
          try {
            const res = await api<{ message: string; report: any }>('/api/notion/sync', { method: 'POST' });
            toast(`Notion synced: ${res.report?.daily || 0} tasks updated!`, 'success');
            setTimeout(() => {
              window.location.reload();
            }, 1000);
          } catch (err) {
            toast(`Sync failed: ${err}`, 'error');
            syncBtn.disabled = false;
            syncBtn.innerHTML = `<span data-lucide="refresh-cw" class="w-3.5 h-3.5"></span> Sync Notion`;
          }
        });
      }
    }).catch((err) => {
      console.error('Failed to load calendar:', err);
    });
  };

  loadCalendar();
}