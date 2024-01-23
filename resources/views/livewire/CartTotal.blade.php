<div>
    <div class="row d-flex">
        <div style="width: 100%">
            <div class="columnTotalDesc md-auto">
                <p>Total Dinheiro - (F1)</p>
                <p>Total Pix, Débito e Crédito(até 2x) - (F2)</p>
                <p>Total Crédito (3x à 6x) - (F3)</p>
            </div>
            <div class="column ml-auto">
                <p>
                    <?php
                    /** @var TYPE_NAME $total */
                    echo "R$ ".number_format($total,2,",",".");
                    ?>
                </p>
                <p>
                    <?php
                    /** @var TYPE_NAME $totalPixDebitoCredito */
                    echo "R$ ".number_format($totalPixDebitoCredito,2,",",".");
                    ?>
                </p>
                <p>
                    <?php
                    /** @var TYPE_NAME $totalCredito */
                    echo "R$ ".number_format($totalCredito,2,",",".");
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>
