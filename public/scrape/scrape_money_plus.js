import { remote } from "webdriverio";
import axios from "axios";

export default class MoneyPlusScraper {
    constructor(adbName, appPackage, appActivity, apiBaseUrl = "http://127.0.0.1:8000") {
        this.adbName = adbName;
        this.appPackage = appPackage;
        this.appActivity = appActivity;
        this.driver = null;
        this.wallets = [];
        this.apiBaseUrl = apiBaseUrl;
    }

    async init() {
        const caps = {
            platformName: "Android",
            "appium:deviceName": this.adbName,
            "appium:automationName": "UiAutomator2",
            "appium:appPackage": this.appPackage,
            "appium:appActivity": this.appActivity,
            "appium:noReset": true,
            "appium:newCommandTimeout": 3600,
        };

        this.driver = await remote({
            protocol: "http",
            hostname: "127.0.0.1",
            port: 4723,
            path: "/",
            capabilities: caps,
        });

        console.log("✅ Appium siap di device!");
    }

    async clickDompetTab() {
        try {
            console.log("🔍 Mencari tab Dompet...");

            const dompetTab = await this.driver.$(
                'android=new UiSelector().description("Dompet\nTab 2 dari 4")'
            );

            if (await dompetTab.waitForExist({ timeout: 5000 })) {
                await dompetTab.click();
                console.log("✅ Tab Dompet diklik");
                await this.driver.pause(2000); // Tunggu loading
                return true;
            } else {
                console.log("❌ Tab Dompet tidak ditemukan");
                return false;
            }
        } catch (error) {
            console.log("❌ Gagal klik tab Dompet:", error.message);
            return false;
        }
    }

    parseWalletDescription(description) {
        // Format: "Nama Wallet\nRp123.456\nIDR(1.0)"
        const lines = description.split("\n").map(line => line.trim()).filter(line => line);

        if (lines.length < 3) return null;

        // Cari index yang mengandung "IDR"
        const idrIndex = lines.findIndex(line => line.includes("IDR"));
        if (idrIndex === -1) return null;

        // Index sebelum IDR adalah balance
        const balanceIndex = idrIndex - 1;
        if (balanceIndex < 0) return null;

        // Semua sebelum balance adalah nama wallet
        const name = lines.slice(0, balanceIndex).join(" ");
        const balanceText = lines[balanceIndex];

        // Parse balance: "Rp123.456" -> 123456
        const balance = this.parseBalance(balanceText);

        return {
            name: name,
            balance: balance,
            raw_description: description
        };
    }

    parseBalance(balanceText) {
        // Hapus "Rp", spasi, dan titik pemisah ribuan
        const cleaned = balanceText
            .replace(/Rp/gi, '')
            .replace(/\s/g, '')
            .replace(/\./g, '')
            .replace(/,/g, '');

        const amount = parseInt(cleaned, 10);
        return isNaN(amount) ? 0 : amount;
    }

    async scrapeWallets() {
        console.log("📊 Mulai scraping wallet...");

        const scrapedWallets = new Map(); // Gunakan Map untuk deduplikasi
        let prevSource = "";
        let scrollCount = 0;
        const maxScroll = 10;

        while (scrollCount < maxScroll) {
            const currentSource = await this.driver.getPageSource();

            // Jika page source sama dengan sebelumnya, berarti sudah mentok
            if (currentSource === prevSource) {
                console.log("📍 Sudah mentok di bawah");
                break;
            }
            prevSource = currentSource;

            // Cari semua elemen dengan description yang mengandung "IDR"
            const elements = await this.driver.$$(
                'android=new UiSelector().descriptionContains("IDR")'
            );

            console.log(`🔍 Ditemukan ${elements.length} elemen dengan IDR di layar ini`);

            for (let element of elements) {
                try {
                    const description = await element.getAttribute("contentDescription");

                    // Parse wallet data
                    const walletData = this.parseWalletDescription(description);

                    if (walletData && walletData.name) {
                        // Gunakan nama wallet sebagai key untuk deduplikasi
                        const key = walletData.name.toLowerCase().trim();

                        if (!scrapedWallets.has(key)) {
                            scrapedWallets.set(key, walletData);
                            console.log(`✅ Wallet: "${walletData.name}" - Saldo: Rp${walletData.balance.toLocaleString('id-ID')}`);
                        }
                    }
                } catch (err) {
                    // Skip elemen yang error
                    continue;
                }
            }

            // Scroll ke bawah untuk mencari wallet lainnya
            await this.scrollDown();
            scrollCount++;
            await this.driver.pause(1000);
        }

        // Convert Map to Array
        this.wallets = Array.from(scrapedWallets.values());

        console.log(`\n📦 Total wallet berhasil di-scrape: ${this.wallets.length}`);
        return this.wallets;
    }

    async scrollDown() {
        const { width, height } = await this.driver.getWindowSize();
        const startX = width / 2;
        const startY = height * 0.8;
        const endY = height * 0.2;

        await this.driver.performActions([{
            type: 'pointer',
            id: 'finger1',
            parameters: { pointerType: 'touch' },
            actions: [
                { type: 'pointerMove', duration: 0, x: startX, y: startY },
                { type: 'pointerDown', button: 0 },
                { type: 'pause', duration: 300 },
                { type: 'pointerMove', duration: 500, x: startX, y: endY },
                { type: 'pointerUp', button: 0 }
            ]
        }]);
        await this.driver.releaseActions();
    }

    async syncToDatabase() {
        console.log("\n💾 Mulai sinkronisasi ke database...");

        try {
            // Kirim data wallet ke Laravel API
            const response = await axios.post(
                `${this.apiBaseUrl}/api/wallets/sync-from-scraper`,
                {
                    wallets: this.wallets
                },
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }
            );

            if (response.data.success) {
                console.log("✅ Sinkronisasi berhasil!");
                console.log(`   - Created: ${response.data.created || 0} wallet`);
                console.log(`   - Updated: ${response.data.updated || 0} wallet`);
                console.log(`   - Skipped: ${response.data.skipped || 0} wallet`);
                return response.data;
            } else {
                console.log("❌ Sinkronisasi gagal:", response.data.message);
                return null;
            }
        } catch (error) {
            console.log("❌ Error saat sinkronisasi:", error.message);
            if (error.response) {
                console.log("   Response:", error.response.data);
            }
            return null;
        }
    }

    async run() {
        try {
            await this.init();

            // Step 1: Klik tab Dompet
            const tabClicked = await this.clickDompetTab();
            if (!tabClicked) {
                console.log("❌ Gagal membuka tab Dompet. Proses dihentikan.");
                return;
            }

            // Step 2: Scrape semua wallet
            await this.scrapeWallets();

            if (this.wallets.length === 0) {
                console.log("⚠️ Tidak ada wallet yang ditemukan");
                return;
            }

            // Step 3: Sinkronisasi ke database
            await this.syncToDatabase();

            console.log("\n🎉 Proses selesai!");
        } catch (error) {
            console.log("❌ Error:", error.message);
        } finally {
            if (this.driver) {
                await this.driver.deleteSession();
                console.log("👋 Session Appium ditutup");
            }
        }
    }
}
