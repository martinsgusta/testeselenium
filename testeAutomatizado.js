const { Builder, By, until } = require("selenium-webdriver");
const fs = require("fs");
const path = require("path");
let relatorio = [];

// ---------- CONFIGURAÇÃO ----------
const TARGET_URL = "http://localhost/Loginphp/index.php"; // Corrigido para o seu projeto
const SCREENSHOT_DIR = path.join(__dirname, "assets", "screenshots");
const TIMEOUT_MS = 5000; // tempo de espera padrão

fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });

function salvarScreenshot(base64, nomeArquivo) {
	const filePath = path.join(SCREENSHOT_DIR, nomeArquivo);
	fs.writeFileSync(filePath, base64, "base64");
	return filePath;
}

// ---------- FUNÇÃO DE TESTE (padrão) ----------
async function testarLogin(email, senha, descricao) {
	let driver = await new Builder().forBrowser("chrome").build();
	let status = "pass";
	let mensagem = "";
	try {
		console.log(`\nTestando: ${descricao}`);
		await driver.get(TARGET_URL);

		await driver.wait(until.elementLocated(By.id("email")), TIMEOUT_MS);
		await driver.findElement(By.id("email")).sendKeys(email);

		await driver.wait(until.elementLocated(By.id("senha")), TIMEOUT_MS);
		await driver.findElement(By.id("senha")).sendKeys(senha);

		await driver.wait(until.elementLocated(By.id("btn-login")), TIMEOUT_MS);
		await driver.findElement(By.id("btn-login")).click();

		await driver.wait(until.elementLocated(By.id("mensagem")), TIMEOUT_MS);
		mensagem = await driver.findElement(By.id("mensagem")).getText();
		console.log("Mensagem recebida:", mensagem);

		const safeName = descricao
			.replace(/\s+/g, "_")
			.replace(/[^a-zA-Z0-9_\-]/g, "");
		const screenshotName = `screenshot_${safeName}.png`;
		const base64 = await driver.takeScreenshot();
		const savedPath = salvarScreenshot(base64, screenshotName);
		console.log(`Screenshot salva em: ${savedPath}`);
		relatorio.push({
			teste: descricao,
			status,
			mensagem,
			screenshot: savedPath,
		});
	} catch (err) {
		status = "fail";
		console.log("Erro durante o teste:", err.message);
		try {
			const safeName = descricao
				.replace(/\s+/g, "_")
				.replace(/[^a-zA-Z0-9_\-]/g, "");
			const screenshotName = `screenshot_erro_${safeName}.png`;
			const base64 = await driver.takeScreenshot();
			const savedPath = salvarScreenshot(base64, screenshotName);
			console.log(`Screenshot de erro salva em: ${savedPath}`);
			relatorio.push({
				teste: descricao,
				status,
				mensagem,
				screenshot: savedPath,
			});
		} catch (e) {
			console.log("Não foi possível salvar screenshot de erro:", e.message);
			relatorio.push({
				teste: descricao,
				status,
				mensagem,
				screenshot: null,
			});
		}
	} finally {
		await driver.quit();
	}
}

// ---------- ARRAY DE TESTES ----------
const testes = [
	{ email: "admin@teste.com", senha: "1234", descricao: "Login correto" },
	{ email: "admin@teste.com", senha: "errada", descricao: "Senha incorreta" },
	{ email: "", senha: "1234", descricao: "Campo email vazio" },
	{ email: "admin@teste.com", senha: "", descricao: "Campo senha vazio" },
	{ email: "<script>", senha: "1234", descricao: "Tentativa de XSS" },
];

// ---------- EXECUÇÃO SEQUENCIAL ----------
(async () => {
	if (!testsOrArrayIsValid(testes)) {
		console.log(
			"Nenhum teste configurado. Edite o array `testes` no arquivo para adicionar casos."
		);
		return;
	}

	for (let t of testes) {
		await testarLogin(t.email, t.senha, t.descricao);
	}

	fs.writeFileSync("relatorio.json", JSON.stringify(relatorio, null, 2));
	console.log("\nRelatório final salvo em relatorio.json");
})();

// ---------- FUNÇÕES AUXILIARES ----------
function testsOrArrayIsValid(arr) {
	return Array.isArray(arr) && arr.length > 0;
}
