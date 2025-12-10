lucide.createIcons();

/* ===========================
   HORA LOCAL + UTC
=========================== */
function actualizarHora() {
  const ahora = new Date();
  const horas = ahora.getHours();
  const min = ahora.getMinutes().toString().padStart(2,'0');

  const horaLocal = `${horas.toString().padStart(2,'0')}:${min}`;

  const ahoraUTC = new Date(ahora.getTime() + ahora.getTimezoneOffset()*60000);
  const horasUTC = ahoraUTC.getUTCHours().toString().padStart(2,'0');
  const minUTC   = ahoraUTC.getUTCMinutes().toString().padStart(2,'0');
  const horaUTC  = `${horasUTC}:${minUTC}`;

  let fondo = "linear-gradient(to right, #56ccf2, #2f80ed)";
  let icono = "🕐";
  let colorTexto = "#fff"; // blanco por defecto

  if (horas >= 5 && horas < 8) {
    fondo = "linear-gradient(to right,#ffefba,#ffffff)";
    icono = "🌅";
    colorTexto = "#000"; // mañana = texto negro
  } else if (horas >= 8 && horas < 18) {
    fondo = "linear-gradient(to right,#56ccf2,#2f80ed)";
    icono = "☀️";
    colorTexto = "#fff"; // día = blanco
  } else if (horas >= 18 && horas < 20) {
    fondo = "linear-gradient(to right,#ff9966,#ff5e62)";
    icono = "🌇";
    colorTexto = "#fff";
  } else {
    fondo = "linear-gradient(to right,#141e30,#243b55)";
    icono = "🌙";
    colorTexto = "#fff"; // noche = blanco
  }

  document.getElementById("horaLocal").textContent = horaLocal;
  document.getElementById("horaUTC").textContent   = horaUTC;

  const horaCard = document.getElementById("horaCard");
  horaCard.style.background = fondo;
  horaCard.style.color = colorTexto;
  horaCard.querySelector("h3").style.color = colorTexto;

  horaCard.querySelector("h3").innerHTML = `<i data-lucide="clock"></i> Hora ${icono}`;
}


setInterval(actualizarHora,1000);
actualizarHora();


/* ===========================
   CLIMA
=========================== */
function cargarClima() {
  fetch("https://wttr.in/" + encodeURIComponent(CONFIG.ciudadClima) + "?format=j1")
    .then(res=>res.json())
    .then(data=>{
      const c = data.current_condition[0];
      const condicion = c.weatherDesc[0].value;
      const temp = c.temp_C;
      const hum  = c.humidity;
      const viento = c.windspeedKmph;
      const tempMax = data.weather[0].maxtempC;
      const tempMin = data.weather[0].mintempC;

      let icon = "🌡️";
      if (condicion.toLowerCase().includes("cloud")) icon = "☁️";
      else if (condicion.toLowerCase().includes("rain")) icon = "🌧️";
      else if (condicion.toLowerCase().includes("snow")) icon = "❄️";
      else if (condicion.toLowerCase().includes("sun") || condicion.toLowerCase().includes("clear")) icon = "☀️";

      let fondo = "linear-gradient(to right,#56ccf2,#2f80ed)";
      if (condicion.toLowerCase().includes("cloud")) fondo = "linear-gradient(to right,#757f9a,#d7dde8)";
      else if (condicion.toLowerCase().includes("rain")) fondo = "linear-gradient(to right,#373b44,#4286f4)";
      else if (condicion.toLowerCase().includes("snow")) fondo = "linear-gradient(to right,#e0eafc,#cfdef3)";

      const card = document.getElementById("climaCard");
      card.style.background = fondo;
      card.classList.add("text-white");

      document.getElementById("climaInfo").innerHTML = `
        <strong>${icon} ${CONFIG.ciudadClima} </strong><br>
        ${temp}°C - 🌡️ Mín: ${tempMin}° / Máx: ${tempMax}° <br>
        💧 ${hum}% - 🌬️ ${viento} km/h <br>
        <small>${condicion}</small>
      `;
    });
}
cargarClima();
setInterval(cargarClima,600000);


/* ===========================
   ESTADO SISTEMA
=========================== */
function cargarSistema() {
  fetch("estado_nodo.php")
    .then(r=>r.json())
    .then(data=>{
      const cpu = parseFloat(data.cpu);
      const ram = parseFloat(data.ram);


      document.getElementById("cpuInfo").innerText = cpu+"%";
      document.getElementById("ramInfo").innerText = ram+"%";
      document.getElementById("soInfo").innerText = data.so ?? "Desconocido";

      let fondo = "linear-gradient(to right,#56ab2f,#a8e063)";
      let alerta = false;

      if ((cpu>=50 && cpu<=80) || (ram>=50 && ram<=80)) {
        fondo = "linear-gradient(to right,#f7971e,#ffd200)";
      }
      if (cpu>80 || ram>80) {
        fondo = "linear-gradient(to right,#e53935,#e35d5b)";
        alerta = true;
      }

      const card = document.getElementById("sistemaCard");
      card.style.background = fondo;
      card.classList.add("text-white");
      if (alerta) card.classList.add("parpadeo");
      else card.classList.remove("parpadeo");
    });
}
cargarSistema();
setInterval(cargarSistema,3000);


/* ===========================
   CARGAR DATOS DEL YSF
=========================== */
function cargarDatosYSF() {
  fetch("data_index.php")
    .then(r=>r.json())
    .then(data=>{

      /* === TX === */
      const txCard = document.getElementById("txCard");
      const txEstado = document.getElementById("txEstado");
      const txUsuario = document.getElementById("txUsuario");

      if (data.tx) {
        txCard.style.backgroundColor = data.tx.estado==="reciente" ? "#4caf50" : "#e53935";
        txEstado.textContent = data.tx.estado==="reciente" ? "Último comunicado" : "Transmitiendo";
        txUsuario.textContent = `${data.tx.de} → ${data.tx.a}`;
      } else {
        txCard.style.backgroundColor = "#eee";
        txEstado.textContent = "Inactivo";
        txUsuario.textContent = "-";
      }

      /* === TABLA UNIFICADA === */
      const tbody = document.querySelector("#tablaUnificada tbody");
      tbody.innerHTML = "";

      const tot = data.totales ?? {};
      document.getElementById("totalRepetidores").innerText = tot.repetidores ?? 0;
      document.getElementById("totalMoviles").innerText     = tot.moviles ?? 0;
      document.getElementById("totalBridges").innerText     = tot.bridges ?? 0;

      const lista = [
        ...(data.repetidores ?? []),
        ...(data.moviles ?? []),
        ...(data.bridges ?? [])
      ];

      lista.forEach(item=>{
        let tipo = "Estación / App";
        let color = "#fff6c9";

        if (item.puerto == 4260) {
          tipo = "Repetidor / Hotspot";
          color = "#d9ecff";
        }
        else if (item.puerto >= 33800 && item.puerto < 33900) {
          tipo = "Bridge";
          color = "#d5f8d5";
        }

        tbody.innerHTML += `
          <tr style="background:${color}">
            <td>${item.indicativo}</td>
            <td>${item.ip}</td>
            <td>${item.puerto}</td>
            <td>${tipo}</td>
            <td>${item.ratio}</td>
          </tr>
        `;
      });

      /* === PODIO === */
      const podioTbody = document.querySelector("#tablaPodio tbody");
      podioTbody.innerHTML = "";

      (data.podio ?? []).forEach((u,i)=>{
        const m = i==0?"🥇":i==1?"🥈":i==2?"🥉":i+1;
        podioTbody.innerHTML += `
          <tr>
            <td>${m}</td>
            <td>${u.indicativo}</td>
            <td>${u.tx}</td>
            <td>${u.ultima}</td>
          </tr>
        `;
      });

      document.getElementById("totalUsuarios").innerText = (data.podio ?? []).length;

      /* === ÚLTIMOS 5 === */
      const lastTbody = document.querySelector("#tablaLastHeard tbody");
      lastTbody.innerHTML = "";

      (data.last_heard ?? []).forEach(e=>{
        lastTbody.innerHTML += `
          <tr>
            <td>${e.hora}</td>
            <td>${e.de}</td>
            <td>${e.a}</td>
          </tr>
        `;
      });

    });
}

setInterval(cargarDatosYSF,5000);
cargarDatosYSF();
