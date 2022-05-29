import moment from "moment";
import React, { useEffect, useState } from "react";
import Pagination from "../components/Pagination";
import {
    default as invoicesAPI,
    default as InvoicesAPI
} from "../services/InvoicesAPI";

const STATUS_CLASSES = {
  PAID: "bg-success",
  SENT: "bg-primary",
  CANCELLED: "bg-danger",
};

const STATUS_NAMES = {
  PAID: "Payée",
  SENT: "Envoyée",
  CANCELLED: "Annulée",
};

const InvoicesPage = () => {
  const [invoices, setInvoices] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [search, setSearch] = useState("");

  useEffect(() => {
    invoicesAPI
      .findAll()
      .then((data) => setInvoices(data))
      .catch((error) => console.log(error.response));
  }, []);

  const handlePagination = (page) => {
    setCurrentPage(page);
  };

  const handleSearch = ({ currentTarget }) => {
    setSearch(currentTarget.value);
    setCurrentPage(1);
  };

  const handleDelete = (id) => {
    // store current invoices
    const originalInvoices = [...invoices];

    // filter customers without the deleted one
    setInvoices(invoices.filter((invoice) => invoice.id !== id));

    InvoicesAPI.delete(id)
      .then((response) => console.log("ok"))
      .catch((error) => {
        // if error during deleting, set invoices back to the original one
        setInvoices(originalInvoices);
        console.log(error.response);
      });
  };

  const itemsPerPage = 10;
  const filteredInvoices = invoices.filter(
    (i) =>
      i.chrono.toString().includes(search.toLowerCase()) ||
      i.customer.firstName.toLowerCase().includes(search.toLowerCase()) ||
      i.customer.lastName.toLowerCase().includes(search.toLowerCase()) ||
      i.amount.toString().startsWith(search.toLowerCase()) ||
      STATUS_NAMES[i.status].toLowerCase().includes(search.toLowerCase())
  );
  const paginatedInvoices = Pagination.getData(
    filteredInvoices,
    currentPage,
    itemsPerPage
  );

  const formatDate = (str) => moment(str).format("DD/MM/YYYY");

  return (
    <>
      <div className="mt-5 p-3">
        <h1>Liste des factures</h1>
        <div className="form-group pt-4 pb-4">
          <input
            type="text"
            className="form-control"
            placeholder="Rechercher"
            onChange={handleSearch}
            value={search}
          />
        </div>
      </div>
      <table className="table table-hover">
        <thead>
          <tr>
            <th>Numéro</th>
            <th>Client</th>
            <th className="text-center">Date d'envoi</th>
            <th className="text-center">Statut</th>
            <th className="text-center">Montant</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {paginatedInvoices.map((invoice) => (
            <tr key={invoice.id}>
              <td>{invoice.chrono}</td>
              <td>
                <a href="">
                  {invoice.customer.firstName} {invoice.customer.lastName}
                </a>
              </td>
              <td className="text-center">{formatDate(invoice.sentAt)}</td>
              <td className="text-center">
                <span
                  className={
                    "badge rounded-pill " + STATUS_CLASSES[invoice.status]
                  }
                >
                  {STATUS_NAMES[invoice.status]}
                </span>
              </td>
              <td className="text-center">
                {invoice.amount.toLocaleString()} €
              </td>
              <td>
                <button type="button" className="btn btn-sm btn-warning">
                  Modifier
                </button>
                &nbsp;
                <button
                  type="button"
                  className="btn btn-sm btn-danger"
                  onClick={() => handleDelete(invoice.id)}
                >
                  Supprimer
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      <Pagination
        currentPage={currentPage}
        itemsPerPage={itemsPerPage}
        length={filteredInvoices.length}
        handlePagination={handlePagination}
      />
    </>
  );
};

export default InvoicesPage;
