document.getElementById("botaoCsv").addEventListener("click", function () {
  const ids = [
    ["ph_antes", "ph_depois", "pH"],
    ["turb_antes", "turb_depois", "Turbidez (uT)"],
    ["cloro_antes", "cloro_depois", "Cloro (mg/L)"],
    ["dur_antes", "dur_depois", "Dureza (mg/L)"],
    ["temp_antes", "temp_depois", "Temperatura (°C)"],
    ["solidos_antes", "solidos_depois", "TDS (mg/L)"],
  ];
  const linhas = [["parametro", "antes", "depois"]];
  ids.forEach(([a, b, rotulo]) => {
    const va = document.getElementById(a)?.value ?? "";
    const vb = document.getElementById(b)?.value ?? "";
    linhas.push([rotulo, va, vb]);
  });
  const csv = linhas
    .map((r) =>
      r.map((v) => '"' + String(v).replaceAll('"', '""') + '"').join(","),
    )
    .join("\r\n");
  const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "dataset-agua-ods6.csv";
  document.body.appendChild(link);
  link.click();
  setTimeout(() => {
    URL.revokeObjectURL(url);
    link.remove();
  }, 500);
});
