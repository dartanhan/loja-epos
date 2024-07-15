<div>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <button class="btn btn-dark" onclick="toggleNav()">&#9776;</button>
        <a class="navbar-brand ml-3" href="#">KNPOS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="mx-auto w-50">
                <input type="text" name="searchProduct" id="c"
                        wire:model="barcode" class="form-control custom-disabled btn-sm" 
                        placeholder="Pesquisar produtos..." autofocus>
            </div>
        </div>
    </nav>
</div>