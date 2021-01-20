export default {
  template: `
    <div class="barcode-box">
      <div class="code text-right">{{ producto.Fecha }}</div>
      <!--<div class="text-right direccion">URUBAMBA MZA. 11<br> LOTE. 3D </div>-->
      <div class="barcode-logo"><img src="/resources/images/logo-ticket.svg"  style="height:25px; margin: .5mm"/></div>
      <div class="barcode-right">
          <p>PRECIO S/.</p>
          <label>{{ (producto.PrecioContado || 0).toFixed(2) }}</label>
          <!--<p>TALLA</p>
          <label>{{ producto.ProductoTalla }}</label>-->
      </div>

      <div class="barcode-left">
        <!--<div class="item negro">ART.</div>--><div class="item derecha">{{ producto.Producto.substring(0, 12).toUpperCase() }}</div><br />
        <!--<div class="item negro">MOD.</div>--><div class="item derecha">{{ producto.ProductoModelo.substring(0, 12).toUpperCase() }}</div><br />
        <!--<div class="item negro">MAR.</div><div class="item derecha" style = "float: left">{{ producto.ProductoMarca }}</div><br />-->
        <!--<div class="item negro">COLOR</div><div class="item derecha">{{ producto.Color }}</div>
        <div class="item negro">PLAZA</div><div class="item derecha">{{ producto.Botapie }}</div>-->
        <div class="barcode-image">
            <barcode :value="producto.CodigoBarra" tag="svg" :options="{ width: .75, margin:5, height: 18, fontSize: 7, marginTop: 1, marginBottom: 1}" ></barcode>
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