<?php
// Shared quest-hero. Params (all optional except $heroTitle):
//   $heroTitle (string), $heroSub (string), $heroChips (html), $heroStats (html),
//   $heroSide (html, right column e.g. model-viewer), $heroParticles (bool, default true),
//   $heroCenter (bool, default false)
$p = $heroParticles ?? true;
$center = $heroCenter ?? false;
?>
<section class="quest-hero p-5 sm:p-7<?= $center ? ' text-center' : '' ?>" data-hero data-reveal>
  <?php if ($p): ?><div id="particles-hero" data-particles aria-hidden="true"></div><?php endif; ?>
  <div class="relative z-10<?= isset($heroSide) ? ' grid gap-5 lg:grid-cols-12 items-center' : '' ?>">
    <div class="<?= isset($heroSide) ? 'lg:col-span-8 min-w-0 space-y-3' : 'space-y-3' ?>" <?= $center ? '' : 'data-hero-item' ?>>
      <?php if (!empty($heroChips)): ?><div class="flex flex-wrap items-center gap-2<?= $center ? ' justify-center' : '' ?>"><?= $heroChips ?></div><?php endif; ?>
      <h1 class="text-2xl font-extrabold text-white sm:text-3xl tracking-tight leading-snug" data-split><?= esc($heroTitle ?? '') ?></h1>
      <?php if (!empty($heroSub)): ?><p class="text-sm text-slate-300/90"><?= esc($heroSub) ?></p><?php endif; ?>
      <?php if (!empty($heroStats)): ?><div class="flex flex-wrap items-center gap-2<?= $center ? ' justify-center' : '' ?>"><?= $heroStats ?></div><?php endif; ?>
    </div>
    <?php if (isset($heroSide)): ?>
    <div class="lg:col-span-4 min-w-0" data-hero-item><?= $heroSide ?></div>
    <?php endif; ?>
  </div>
</section>
