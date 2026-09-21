<?php
// Gentelella x-panel port (dark-adapted). Params:
//   $panelTitle (string, required), $panelSub (string, opt), $panelIcon (lucide name, opt),
//   $panelBody (html string, required), $panelFoot (html, opt link footer),
//   $panelCollapse (bool, default true), $panelClass (extra classes, opt),
//   $panelReveal (bool, default true), $panelTilt (bool, default false)
$collapse = $panelCollapse ?? true;
$cls = 'study-card x-panel p-5 ' . ($panelClass ?? '');
?>
<div class="<?= $cls ?>" <?= ($panelReveal ?? true) ? 'data-reveal' : '' ?> <?= ($panelTilt ?? false) ? 'data-tilt' : '' ?>>
  <div class="x-title">
    <?php if (!empty($panelIcon)): ?>
    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg border border-white/10 bg-white/5 text-violet-300">
      <span data-lucide="<?= esc($panelIcon) ?>" class="h-4 w-4"></span>
    </span>
    <?php endif; ?>
    <h2><?= esc($panelTitle ?? '') ?><?php if (!empty($panelSub)): ?><small><?= esc($panelSub) ?></small><?php endif; ?></h2>
    <?php if ($collapse): ?>
    <div class="panel_toolbox">
      <button type="button" data-panel-collapse aria-label="Collapse panel" data-tip="Collapse panel">
        <span data-lucide="chevron-up" class="collapse-icon h-4 w-4"></span>
      </button>
    </div>
    <?php endif; ?>
  </div>
  <div class="x-content"><?= $panelBody ?? '' ?></div>
  <?php if (!empty($panelFoot)): ?><a class="dash-box-footer" href="#"><?= $panelFoot ?></a><?php endif; ?>
</div>
