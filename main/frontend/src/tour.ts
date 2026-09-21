import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';

export const TOUR_KEY = 'gate_tour_done';

interface Step {
  element: string;
  title: string;
  desc: string;
}

const STEPS: Record<string, Step[]> = {
  dashboard: [
    { element: '#btn-done-mission', title: 'Daily mission', desc: 'This is your task for today from the Notion plan. Mark it done for +20 XP.' },
    { element: '#viz3d', title: 'Mastery constellation', desc: 'Each glowing planet is a subject, sized by your progress. Click one to practice it.' },
    { element: '#accuracy-chart', title: 'Accuracy trend', desc: 'Live chart from your quiz history — watch the line climb.' },
    { element: '#heatmap', title: 'Study calendar', desc: '120 days of study activity. Dark squares are rest days.' },
    { element: '#btn-timer', title: 'Study timer', desc: 'Start a focus session — logged minutes earn XP and feed the heatmap.' },
  ],
  course: [
    { element: '.card-glow', title: 'Roadmap phases', desc: 'Your GATE syllabus split into phases. Bars fill as you complete topics.' },
    { element: '#btn-topic-done', title: 'Complete topics', desc: 'On a topic page, mark it complete for +20 XP — progress syncs to Notion.' },
    { element: '#course-quiz', title: 'Topic practice', desc: 'Instant MCQs under each topic with instant feedback.' },
  ],
  training: [
    { element: '.mode-btn', title: 'Pick a mode', desc: 'Quick, timed, random or full 65-question mocks. Keys 1–4 answer, Enter moves next.' },
    { element: '#quiz-subject', title: 'Filter by subject', desc: 'Drill one subject — or leave it on all subjects for mixed practice.' },
    { element: '#quiz-stage', title: 'Quiz stage', desc: 'Questions appear here with a live timer, streak feedback and explanations.' },
    { element: '#btn-new-mcq', title: 'Grow the bank', desc: 'Add MCQs manually, bulk-paste, or generate level-adaptive ones with AI.' },
  ],
  performance: [
    { element: '#chart-accuracy', title: 'Accuracy over time', desc: 'Every quiz you finish moves this line.' },
    { element: '#weak-areas', title: 'Weak areas', desc: 'Auto-detected low-accuracy subjects with one-click training links.' },
    { element: '#chart-targets', title: 'Mock trajectory', desc: 'Your targets from October to GATE 2027, synced from Notion.' },
  ],
  calendar: [
    { element: '#calendar', title: 'Day-by-day plan', desc: '145 scheduled days from Notion. Click any day to toggle done — it syncs back.' },
  ],
  documents: [
    { element: '#doc-search', title: 'Fuzzy search', desc: 'Press / anywhere to jump here. Search matches titles and subjects instantly.' },
    { element: '#doc-grid', title: 'Document hub', desc: 'PDFs open in the built-in viewer; tag any doc to a subject from the card.' },
  ],
  settings: [
    { element: '#settings-form', title: 'Notion sync', desc: 'Token + page ids power the two-way sync. Hit Sync Notion now after editing.' },
    { element: '#ai-settings-form', title: 'Groq AI', desc: 'Powers adaptive MCQs, the chat assistant and the Chrome extension.' },
    { element: '#checklists', title: 'Daily checklists', desc: 'Toggling here updates your Notion to-dos in real time.' },
  ],
};

export function markTourDone(): void {
  try {
    localStorage.setItem(TOUR_KEY, '1');
  } catch {
    /* ignore */
  }
}

function buildSteps(page: string) {
  return (STEPS[page] || [])
    .filter((s) => document.querySelector(s.element))
    .map((s) => ({
      element: s.element,
      popover: { title: s.title, description: s.desc },
    }));
}

function drive(page: string): void {
  const steps = buildSteps(page);
  if (steps.length === 0) return;
  const d = driver({
    showProgress: true,
    progressText: '{{current}} / {{total}}',
    nextBtnText: 'Next →',
    prevBtnText: '← Back',
    doneBtnText: 'Start training 🚀',
    onDestroyed: () => markTourDone(),
    steps,
  });
  d.drive();
}

export function maybeStartTour(page: string): void {
  let done = false;
  try {
    done = !!localStorage.getItem(TOUR_KEY);
  } catch {
    done = true;
  }
  if (done) return;
  // Let charts/widgets paint first so highlight targets exist
  window.setTimeout(() => drive(page), 1000);
}

export function startTour(page: string): void {
  drive(page);
}
