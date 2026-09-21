import './style.css';
import {
  Zap,
  Flame,
  Bell,
  BellRing,
  Calendar,
  CalendarClock,
  ChevronUp,
  LayoutDashboard,
  Compass,
  Target,
  TrendingUp,
  CalendarDays,
  FolderKanban,
  Sliders,
  Clock,
  Award,
  HelpCircle,
  CheckCircle2,
  PlayCircle,
  Lock,
  BookOpen,
  FileText,
  ExternalLink,
  Eye,
  Edit3,
  Plus,
  ClipboardList,
  RotateCcw,
  Sparkles,
  Bot,
  Trophy,
  Timer,
  Dices,
  Flag,
  Brain,
  ChevronRight,
  Search,
  Crosshair,
  Activity,
  Check,
  RefreshCw,
  SlidersHorizontal,
  Database,
  Layers,
  Volume2,
  VolumeX,
  Pause,
  Play,
  Gamepad2,
} from 'lucide';
import { initIcons } from './ui';
import { initFx } from './fx';

const page = document.body.dataset.page || '';

function importModule(name: string): Promise<any> {
  switch (name) {
    case 'dashboard': return import('./dashboard');
    case 'course': return import('./course');
    case 'training': return import('./training');
    case 'performance': return import('./performance');
    case 'documents': return import('./documents');
    case 'settings': return import('./settings');
    case 'calendar': return import('./calendar');
    case 'game': return import('./game');
    default: return Promise.resolve(null);
  }
}

initFx();
void initIcons({
  Zap,
  Flame,
  Bell,
  BellRing,
  Calendar,
  CalendarClock,
  LayoutDashboard,
  Compass,
  Target,
  TrendingUp,
  CalendarDays,
  FolderKanban,
  Sliders,
  Clock,
  Award,
  HelpCircle,
  CheckCircle2,
  PlayCircle,
  Lock,
  BookOpen,
  FileText,
  ExternalLink,
  Eye,
  Edit3,
  Plus,
  ClipboardList,
  RotateCcw,
  Sparkles,
  Bot,
  Trophy,
  Timer,
  Dices,
  Flag,
  Brain,
  ChevronRight,
  Search,
  Crosshair,
  Activity,
  Check,
  RefreshCw,
  SlidersHorizontal,
  Database,
  Layers,
  ChevronUp,
  Volume2,
  VolumeX,
  Pause,
  Play,
  Gamepad2,
});

importModule(page)
  .then((m) => m && m.init && m.init())
  .catch((e) => console.error('[GATE] module init failed', page, e));

// Daily motivation: quote tickers + one gentle toast (lazy, offline-first).
void import('./motivation')
  .then((m) => m.initMotivation(page))
  .catch((e) => console.error('[GATE] motivation failed', e));

// AI chat widget: zero-cost until first click — KaTeX + highlight (~300KB)
// download only when the user actually opens the assistant.
document.getElementById('ai-launcher-static')?.addEventListener(
  'click',
  () => {
    void import('./ai-chat')
      .then((m) => m.initAiChat(true))
      .catch((e) => console.error('[GATE] ai-chat failed', e));
  },
  { once: true },
);

// First-visit onboarding tour (driver.js only loads when needed).
try {
  if (!localStorage.getItem('gate_tour_done')) {
    void import('./tour')
      .then((m) => m.maybeStartTour(page))
      .catch((e) => console.error('[GATE] tour failed', e));
  }
} catch {
  /* storage unavailable — skip tour */
}
