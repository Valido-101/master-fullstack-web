<!-- Usamos la variable titulo que nos han pasado desde PruebasController y la imprimimos en un h1 -->
<h1>{{$titulo}}</h1>

<ul>
    <!-- Recorremos la variable animales que nos han pasado desde PruebasController con un foreach -->
    @foreach($animales as $animal)
        <li>{{$animal}}</li>
    @endforeach
</ul>

