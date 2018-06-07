export default {
    template: `
      <div class="barcode-box">
        <div class="text-right direccion">JR. HUÁNUCO 613</div>
        <div>ROJAS SPORT</div>
        <div class="barcode-right">
            <p>PRECIO S/.</p>
            <label>{{ producto.PrecioContado }}</label>
            <p>TALLA</p>
            <label>{{ producto.ProductoTalla }}</label>
        </div>
        <div class="barcode-left">
            <div class="item negro">ARTICULO</div><div class="item derecha">{{ producto.Producto.substring(0, 12).toUpperCase() }}</div><br />
            <div class="item negro">MARCA</div><div class="item derecha">{{ producto.ProductoMarca }}</div><br />
            <div class="item negro">MODELO</div><div class="item derecha">{{ producto.ProductoModelo }}</div><br />
            <div class="item negro">COLOR</div><div class="item derecha">{{ producto.Color }}</div>
            <div class="barcode-image">
                <barcode :value="producto.CodigoBarra" tag="svg" :options="{ width: 1.2, margin:5, height: 30, fontSize: 11}" ></barcode>
            </div>
            <span></span>
        </div>
        
      </div>
    `,
    mounted() {

    },
    data() {
      return {
      }
    },
    props: ['producto'],
    methods: {
    }
  }