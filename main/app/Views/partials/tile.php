<?php
// Gentelella tile-stats port (dark-adapted). Params:
//   $tileCount (string, required), $tileLabel (string), $tileIcon (lucide name),
//   $tileFoot (html, opt trend line), $tileLink (url, opt), $tileLinkLabel (string, opt)
?>
<div class="study-card tile-stats p-5 hov-lift" data-reveal data-tilt>
  <?php if (!empty($tileIcon)): ?>
  <span class="t-icon" aria-hidden="true"><span data-lucide="<?= esc($tileIcon) ?>"></span></span>
  <?php endif; ?>
  <div class="t-count"><?= $tileCount ?? '' ?></div>
  <?php if (!empty($tileLabel)): ?><h3><?= esc($tileLabel) ?></h3><?php endif; ?>
  <?php if (!empty($tileFoot)): ?><span class="t-foot"><?= $tileFoot ?></span><?php endif; ?>
  <?php if (!empty($tileLink)): ?>
  <a class="dash-box-footer" href="<?= esc($tileLink) ?>"><?= esc($tileLinkLabel ?? 'View details →') ?></a>
  <?php endif; ?>
</div>
