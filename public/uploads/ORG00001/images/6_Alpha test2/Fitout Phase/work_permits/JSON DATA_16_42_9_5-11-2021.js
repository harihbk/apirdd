[

      //dashboad
      {
        name: "Dashboard",
        active: 0,
        subcategories: [ //no need of this array
        ]
      },
      //booking
      {
        name: "Booking",
        active: 0,
        subcategories: [ //no need of this array
        ]
      },
      //transactions
      {
        name: "Transactions",
        active: 0,
        subcategories: [
          {
            name: "Remittance",
            active: 0,
            modules: [
              { name: "Booking", active: 0 }, { name: "Exchange Rates", active: 0 }
            ]
          },
          {
            name: "Transactions",
            active: 0,
            modules: [
              { name: "Pending Payments", active: 0 }, { name: "Pending Processing", active: 0 },
              { name: "Cancelled Transactions", active: 0 }, { name: "Rejected Transactions", active: 0 },
              { name: "All Transactions", active: 0 }
            ]
          },
          {
            name: "Agents",
            active: 0,
            modules: [
              { name: "Agent Payments", active: 0 }
            ]
          },
          {
            name: "Logs",
            active: 0,
            modules: [
              { name: "Transaction Audit Log", active: 0 }
            ]
          }
        ]
      },
      //reports
      {
        name: "Reports",
        active: 0,
        subcategories: [
          {
            name: "Management Reports",
            active: 0,
            modules: [
              { name: "Posting Report", active: 0 }, { name: "Sales Reports", active: 0 },
              { name: "Forex Ledger", active: 0 }, { name: "Payment Report", active: 0 }
            ]
          },
          {
            name: "Transaction Reports",
            active: 0,
            modules: [
              { name: "Transaction", active: 0 }, { name: "Transaction Processing", active: 0 },
              { name: "Reprocessed Transaction", active: 0 }, { name: "Transaction Profit", active: 0 },
              { name: "Refunded Transaction", active: 0 }, { name: "Amended Transaction", active: 0 }
            ]
          },
          {
            name: "Customer Reports",
            active: 0,
            modules: [
              { name: "New Customer Report", active: 0 }, { name: "Customer Ledger", active: 0 },
              { name: "Customer Transaction", active: 0 }, { name: "Customer Log", active: 0 },
              { name: "Customer Device Details", active: 0 }, { name: "Customer Analysis", active: 0 }
            ]
          },
          {
            name: "Beneficiary Reports",
            active: 0,
            modules: [
              { name: "Beneficary Transaction", active: 0 }
            ]
          },
          {
            name: "Agent Reports",
            active: 0,
            modules: [
              { name: "Agent Transaction", active: 0 }, { name: "Agent Summary", active: 0 },
              { name: "Agent Ledger", active: 0 }, { name: "Agent Payment Report", active: 0 }
            ]
          },
          {
            name: "AML Reports",
            active: 0,
            modules: [
              { name: "Currency AML", active: 0 }, { name: "Benificiary AML", active: 0 }
            ]
          },
          {
            name: "MAS Reports",
            active: 0,
            modules: [
              { name: "Top Customer", active: 0 }, { name: "Top Beneficary", active: 0 },
              { name: "Top Currency", active: 0 }
            ]
          },
          {
            name: "MAS Quarterly Reports",
            active: 0,
            modules: [
              { name: "Statement of Transactions", active: 0 }
            ]
          },
          {
            name: "Cash Register",
            active: 0,
            modules: [
              { name: "Cash Register Ledger", active: 0 }
            ]
          },
          {
            name: "Teller Reports",
            active: 0,
            modules: [
              { name: "Total for the day", active: 0 }, { name: "Transaction Denominations", active: 0 },
              { name: "Payment Mode Report", active: 0 }
            ]
          },
          {
            name: "Logos",
            active: 0,
            modules: [
              { name: "User Activity Logos", active: 0 }
            ]
          }
        ]
      },
      //Settings
      {
        name: "Settings",
        active: 0,
        subcategories: [
          {
            name: "Master Management",
            active: 0,
            modules: [
              { name: "Customers", active: 0 }, { name: "Benificiaries", active: 0 }, { name: "Agents", active: 0 },
              { name: "Countries", active: 0 }, { name: "Currencies", active: 0 },
              { name: "Service Charge", active: 0 }, { name: "Agent Payout Mapping", active: 0 },
              { name: "Agent Country Mapping", active: 0 }, { name: "Accepted Currencies", active: 0 },
              { name: "Benificiary Countries", active: 0 }, { name: "Occupations", active: 0 }
            ]
          },
          {
            name: "Delivery Methods",
            active: 0,
            modules: [
              { name: "Bank Account", active: 0 }, { name: "Cash Pickup", active: 0 },
              { name: "Home Delivery", active: 0 }, { name: "Mobile Wallet", active: 0 }
            ]
          },
          {
            name: "Cash Register",
            active: 0,
            modules: [
              { name: "Cash Register", active: 0 }, { name: "Cash Register Users", active: 0 }
            ]
          },
          {
            name: "Administration",
            active: 0,
            modules: [
              { name: "Users", active: 0 }, { name: "Roles", active: 0 }
            ]
          },
          {
            name: "Configuration",
            active: 0,
            modules: [
              { name: "Config", active: 0 }, { name: "AML Score Factors", active: 0 }, { name: "Id Card", active: 0 }
              
            ]
          }
        ]
      }

    ]
