import hljs from 'highlight.js/lib/core';
import cpp from 'highlight.js/lib/languages/cpp';
import cLang from 'highlight.js/lib/languages/c';
import python from 'highlight.js/lib/languages/python';
import java from 'highlight.js/lib/languages/java';
import plaintext from 'highlight.js/lib/languages/plaintext';
import 'highlight.js/styles/github-dark.css';
import { marked } from 'marked';
import DOMPurify from 'dompurify';

hljs.registerLanguage('cpp', cpp);
hljs.registerLanguage('c', cLang);
hljs.registerLanguage('python', python);
hljs.registerLanguage('java', java);
hljs.registerLanguage('plaintext', plaintext);

// Lightweight Markdown-lite renderer for AI answers:
// code fences (highlighted) + inline code + bold/italic + paragraphs + glossary terms.
const ALIAS: Record<string, string> = {
  'c++': 'cpp',
  cc: 'cpp',
  h: 'cpp',
  py: 'python',
  js: 'plaintext',
  ts: 'plaintext',
  txt: 'plaintext',
  '': 'plaintext',
};

const GLOSSARY: [string, string][] = [
  ['time complexity', 'How runtime grows with input size n, e.g. O(n log n).'],
  ['space complexity', 'How extra memory grows with input size n.'],
  ['Big-O notation', 'Upper-bound growth rate of an algorithm.'],
  ['master theorem', 'Solves recurrences T(n) = aT(n/b) + f(n) into Theta bounds.'],
  ['recurrence', 'An equation defining a function in terms of smaller inputs.'],
  ['amortized', 'Average cost per operation over a worst-case sequence.'],
  ['NP-complete', 'Hardest problems in NP; no known polynomial solution.'],
  ['deadlock', 'Processes each waiting on resources held by another.'],
  ['paging', 'Memory management splitting address space into fixed pages.'],
  ['throughput', 'Units of work completed per unit time.'],
  ['latency', 'Delay before a transfer or operation begins.'],
  ['cache', 'Small fast memory holding recently used data.'],
  ['normalization', 'Organizing relational data to remove redundancy.'],
  ['ACID', 'Atomicity, Consistency, Isolation, Durability for transactions.'],
  ['OSI model', 'Seven-layer networking reference model.'],
  ['TCP', 'Reliable, ordered, connection-oriented transport protocol.'],
  ['UDP', 'Fast connectionless transport without delivery guarantees.'],
  ['spanning tree', 'A tree connecting all graph vertices with minimum edges.'],
  ['topological sort', 'Linear ordering of a DAG respecting edge directions.'],
];

function esc(s: string): string {
  return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function inline(s: string): string {
  return esc(s)
    .replace(/`([^`\n]+)`/g, '<code class="rounded bg-slate-700/70 px-1 py-0.5 text-[0.85em] text-amber-200">$1</code>')
    .replace(/\*\*([^*]+)\*\*/g, '<strong class="text-slate-100">$1</strong>')
    .replace(/(^|[\s(])\*([^*\n]+)\*/g, '$1<em>$2</em>');
}

function unesc(s: string): string {
  return s.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#39;/g, "'");
}

function highlightBlocks(html: string): string {
  // marked emits <pre><code class="language-x">escaped</code></pre> — highlight ours.
  return html.replace(/<pre><code(?: class="language-([\w+-]+)")?>([\s\S]*?)<\/code><\/pre>/g, (_m, lang: string, code: string) => {
    const key = ALIAS[(lang || '').toLowerCase()] ?? (lang || 'plaintext');
    return codeBlock(unesc(code), key);
  });
}

function codeBlock(code: string, lang: string): string {
  const key = ALIAS[lang] ?? lang;
  let html: string;
  try {
    html = hljs.getLanguage(key)
      ? hljs.highlight(code.replace(/\n$/, ''), { language: key }).value
      : esc(code);
  } catch {
    html = esc(code);
  }
  return `<pre class="mt-2 overflow-x-auto rounded-lg border border-slate-700 bg-slate-950 p-3 text-xs leading-relaxed"><code class="hljs language-${esc(key)}">${html}</code></pre>`;
}

function glossPart(html: string): string {
  let out = html;
  for (const [term, def] of GLOSSARY) {
    const safeDef = def.replace(/"/g, '&quot;');
    // (?![^<]*>) keeps matches outside of HTML tags
    const re = new RegExp(`(?![^<]*>)\\b(${term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}s?)\\b`, 'gi');
    out = out.replace(re, `<span class="glossary" data-tip="${safeDef}">$1</span>`);
  }
  return out;
}

export function glossarize(html: string): string {
  // Never touch highlighted code blocks
  return html
    .split(/(<pre[\s\S]*?<\/pre>)/g)
    .map((part, i) => (i % 2 === 1 ? part : glossPart(part)))
    .join('');
}

export function renderRichMarkdown(src: string): string {
  let html: string;
  try {
    html = marked.parse(src.replace(/\r/g, ''), { breaks: true, async: false }) as unknown as string;
  } catch {
    html = `<p>${esc(src)}</p>`;
  }
  const rich = glossarize(highlightBlocks(html));
  const clean = DOMPurify.sanitize(rich, { ADD_ATTR: ['target'] });
  return `<div class="ai-md text-sm text-slate-300">${clean || '<p class="text-slate-500">No answer.</p>'}</div>`;
}
