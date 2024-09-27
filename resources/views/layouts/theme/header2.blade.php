<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <button class="btn btn-dark" id="openMenu">&#9776;</button>
        <a class="navbar-brand ml-3" href="#">KNPOS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="mx-auto w-50">
                <div class="position-relative">
                    <input type="text" name="searchProduct" id="searchProduct" wire:model="barcode" class="form-control custom-disabled btn-sm" placeholder="Pesquisar produtos..." autofocus>
                    <div id="spinner" style="position: absolute; top: 50%; right: 10px; width:15px; height: 15px; transform: translateY(-50%); display: none;"></div>
                </div>
            </div>
        </div>
    </nav>
</div>
