export default {
    template: `
      <div class="barcode-box">
        <div class="text-right direccion">JR. HUÁNUCO 613</div>
        <div class="barcode-logo"><img src="/resources/images/barcode-logo.png"  style="height:30px"/></div>
        <div class="barcode-right">
            <p>PRECIO S/.</p>
            <label>{{ producto.PrecioContado.toFixed(2) }}</label>
            <p>TALLA</p>
            <label>{{ producto.ProductoTalla }}</label>
        </div>
        <div class="barcode-left">
            <div class="item negro">ARTICULO</div><div class="item derecha">{{ producto.Producto.substring(0, 12).toUpperCase() }}</div><br />
            <div class="item negro">MARCA</div><div class="item derecha">{{ producto.ProductoMarca }}</div><br />
            <div class="item negro">MODELO</div><div class="item derecha">{{ producto.ProductoModelo }}</div><br />
            <div class="item negro">COLOR</div><div class="item derecha">{{ producto.Color }}</div>
            <div class="item negro">BOTAPIE</div><div class="item derecha">{{ producto.Botapie }}</div>
            <div class="barcode-image">
                <barcode :value="producto.CodigoBarra" tag="svg" :options="{ width: 1.2, margin:5, height: 30, fontSize: 11, marginTop: 1, marginBottom: 1}" ></barcode>
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