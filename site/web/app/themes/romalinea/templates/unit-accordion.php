  <a class="" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">
    <h2>Κατηγορίες</h2>
  </a>
<div class="row">
  <div class="col-12">
    <div class="collapse multi-collapse" id="multiCollapseExample1">
  <?php  echo facetwp_display( 'facet', 'product_categories' ); ?>
    </div>
  </div>

  <div class="col-12">
    <div class="collapse multi-collapse" id="multiCollapseExample2">
      <?php  echo facetwp_display( 'facet', 'product_size' ); ?>
    </div>
  </div>

</div>
